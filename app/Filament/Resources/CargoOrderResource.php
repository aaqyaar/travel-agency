<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoOrderResource\Pages;
use App\Filament\Resources\CargoOrderResource\RelationManagers;
use App\Models\CargoOrder;
use App\Models\Customer;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CargoOrderResource extends Resource
{
    protected static ?string $model = CargoOrder::class;

    protected static ?string $navigationGroup = "Orders";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\BelongsToSelect::make('customer_id')
                    ->relationship('customer', 'name')->searchable()
                    ->options(Customer::all()->pluck('name', 'id'))
                    ->required(),

                Forms\Components\TextInput::make('sender_details')
                    ->required(),
                
                Forms\Components\TextInput::make('receiver_details')
                    ->required(),

                Forms\Components\BelongsToSelect::make('cargo_origin_id')
                    ->relationship('fromDestination', 'iata_code')
                    ->getSearchResultsUsing(callback: fn (string $query) => \App\Models\Destination::where('iata_code', 'like', "%{$query}%")
                        ->orWhere('municipality', 'like', "%{$query}%")
                        ->get()
                        ->mapWithKeys(fn ($destination) => [
                            $destination->id => "{$destination->name} ({$destination->iata_code})"
                        ]))
                    ->searchable()
                    ->required(),

                Forms\Components\BelongsToSelect::make('cargo_destination_id')
                    ->relationship('toDestination', 'iata_code')
                    ->getSearchResultsUsing(callback: fn (string $query) => \App\Models\Destination::where('iata_code', 'like', "%{$query}%")
                        ->orWhere('municipality', 'like', "%{$query}%")
                        ->get()
                        ->mapWithKeys(fn ($destination) => [
                            $destination->id => "{$destination->name} ({$destination->iata_code})"
                        ]))
                    ->searchable()
                    ->required(),
                    
                                
                Forms\Components\TextInput::make('item_description')
                    ->required(),

                Forms\Components\TextInput::make('per_weight_cost')->numeric()->required()->suffix('USD'),
                Forms\Components\TextInput::make('weight')
                        ->reactive()
                        ->numeric()
                        ->required()
                        ->suffix('KG')
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            self::calcShippingPrice($get, $set);
                            self::calcAmount($get, $set);
                            self::calcTotalRevenue($get, $set);
                        }),

                Forms\Components\TextInput::make('shipping_price')->readOnly()
                            ->reactive()->numeric()->required()->suffix('USD'),
                
                Forms\Components\TextInput::make('discount')->default('0')
                            ->reactive()->numeric()->required()->suffix('USD')->afterStateUpdated(function ($state, callable $get, callable $set) {
                               self::calcAmount($get, $set);
                }),

                // Forms\Components\TextInput::make('total_amount')->readOnly()->numeric()->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->reactive()
                    ->numeric()
                    ->prefix('$')
                    ->readOnly(),
                    // ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    //     self::calcTotalRevenue($get, $set);
                    // }),

                Forms\Components\Select::make('shipping_status')
                    ->options([
                        'pending' => 'Pending',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                    ])
                    ->required(),
                
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'partially_paid' => 'Partially Paid',
                    ])
                    ->required()
                    ->reactive(),
                
                Forms\Components\TextInput::make('partially_paid_amount')
                    ->numeric()
                    ->prefix('$')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        self::calcRemainingAmount($get, $set);
                    })
                    ->visible(fn (callable $get) => $get('payment_status') === 'partially_paid')
                    ->required(fn (callable $get) => $get('payment_status') === 'partially_paid'),
            

                Forms\Components\TextInput::make('remaining_amount')
                    ->numeric()
                    ->reactive()
                    ->prefix('$')
                    ->visible(fn (callable $get) => $get('payment_status') === 'partially_paid')
                    ->required(fn (callable $get) => $get('payment_status') === 'partially_paid'),
            

                Forms\Components\Datepicker::make('dispatch_date')
                    ->required(),

                Forms\Components\TextInput::make('total_expenses')
                    ->reactive()
                    ->readOnly()
                    ->numeric()
                    ->prefix('$'),
                
                Forms\Components\TextInput::make('total_revenue')
                    ->reactive()
                    ->default('0')
                    ->readOnly()
                    ->numeric()
                    ->prefix('$'),
                
                    Forms\Components\Repeater::make('other_expenses')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->numeric()
                                ->reactive()
                                ->required(),
                            Forms\Components\FileUpload::make('attachment')
                                ->disk('public')
                                ->nullable(),
                        ])
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $expenses = $get('other_expenses') ?? [];
                            $totalExpenses = collect($expenses)->sum(function ($expense) {
                                return is_numeric($expense['amount']) ? $expense['amount'] : 0;
                            });
                            $set('total_expenses', $totalExpenses);
                    
                            self::calcTotalRevenue($get, $set);
                        }),

                        Forms\Components\Hidden::make('registered_by')
                        ->default(auth()->id())
                        ->required(),
                                
            ]);
    }

    public static function calcTotalRevenue(callable $get, callable $set)
    {
        $totalAmount = $get('total_amount');
        $totalExpenses = $get('total_expenses');
        if (!is_numeric($totalAmount)) {
            $totalAmount = 0;
        }
        if (!is_numeric($totalExpenses)) {
            $totalExpenses = 0;
        }
        $totalRevenue = $totalAmount - $totalExpenses;
        $set('total_revenue', number_format($totalRevenue, 2));
    }
             

    public static function calcShippingPrice($get, $set) {
        $perWeightCost = $get('per_weight_cost');
        $weight = $get('weight');
        if (empty($perWeightCost) || $perWeightCost == 0) {
            $set('shipping_price', null);
            return;
        }
        if (empty($weight) || $weight == 0) {
            $set('shipping_price', null);
            return;
        }
        $set('shipping_price', $perWeightCost * $weight);

    }
    public static function calcRemainingAmount($get, $set) {
        $total_amount = $get('total_amount');
        if (empty($total_amount) || $total_amount == 0) {
            $set('remaining_amount', null);
            return;
        }
    
        $partially_paid_amount = $get('partially_paid_amount');
        if (empty($partially_paid_amount) || $partially_paid_amount == 0) {
            $set('remaining_amount', $total_amount);
            return;
        }
    
        if ($partially_paid_amount >= $total_amount) {
            $set('remaining_amount', 0);
            return;
        }
        $set('remaining_amount', $total_amount - $partially_paid_amount);
    }
    
    public static function calcAmount($get, $set) {
        $shippingPrice = $get('shipping_price');
        $discount = $get('discount');
        if (empty($shippingPrice) || $shippingPrice == 0) {
            $set('total_amount', null);
            return;
        }
        if (empty($discount) || $discount == 0) {
            $set('total_amount', $shippingPrice);
            return;
        }
        $set('total_amount', $shippingPrice - $discount);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->columns([
                
                Tables\Columns\TextColumn::make('sales_status')
                ->badge()
                ->formatStateUsing(fn ($state) => ucfirst($state))
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                })
                ->searchable(),

                Tables\Columns\TextColumn::make('revenue')
                    ->money()
                    ->searchable(),
                Tables\Columns\TextColumn::make('registered_by.name')
                    ->label('Registered By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCargoOrders::route('/'),
            'create' => Pages\CreateCargoOrder::route('/create'),
            'edit' => Pages\EditCargoOrder::route('/{record}/edit'),
        ];
    }
}
