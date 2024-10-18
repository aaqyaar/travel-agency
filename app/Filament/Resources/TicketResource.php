<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationGroup = "Orders";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\BelongsToSelect::make('customer_id')
                        ->relationship('customer', 'name')->searchable()
                        ->options(Customer::all()->pluck('name', 'id'))
                        ->required(),
                    
                    Forms\Components\BelongsToSelect::make('supplier_id')
                        ->relationship('supplier', 'name')->searchable()
                        ->options(Supplier::all()->pluck('name', 'id'))
                        ->required(),
                        Forms\Components\BelongsToSelect::make('from_destination_id')
                        ->relationship('fromDestination', 'iata_code')
                        ->getSearchResultsUsing(callback: fn (string $query) => \App\Models\Destination::where('iata_code', 'like', "%{$query}%")
                            ->orWhere('municipality', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(fn ($destination) => [
                                $destination->id => "{$destination->name} ({$destination->iata_code})"
                            ]))
                        ->searchable()
                        ->required(),

                    Forms\Components\BelongsToSelect::make('to_destination_id')
                        ->relationship('toDestination', 'iata_code')
                        ->getSearchResultsUsing(fn (string $query) => \App\Models\Destination::where('iata_code', 'like', "%{$query}%")
                            ->orWhere('municipality', 'like', "%{$query}%")
                            ->get()
                            ->mapWithKeys(fn ($destination) => [
                                $destination->id => "{$destination->name} ({$destination->iata_code})"
                            ]))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('passenger_name')
                        ->required(),
                    
                     Forms\Components\Select::make('trip_type')
                        ->options([
                            'one_way' => 'One Way',
                            'round_trip' => 'Round Trip',
                        ])
                        ->required(),
                    Forms\Components\Select::make('ticket_type')
                        ->options([
                            'economy' => 'Economy',
                            'business' => 'Business',
                            'first_class' => 'First Class',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('total_amount')
                        ->required(),
                    Forms\Components\TextInput::make('airline_cost')
                        ->required(),
                    Forms\Components\BelongsToSelect::make('payment_method_id')
                        ->relationship('paymentMethod', 'name')
                        ->required(),
                 
                    Forms\Components\Select::make('sales_status')
                        ->options([
                            'pending' => 'Pending',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('revenue')
                        ->required(),
                    Forms\Components\TextInput::make('booking_reference_number')
                        ->required(),
                    Forms\Components\FileUpload::make('ticket_attachment')
                        ->disk('public')
                        ->required(),  
                    Forms\Components\Repeater::make('other_expenses')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->required(),
                            Forms\Components\FileUpload::make('attachment')
                                 ->disk('public')
                                ->required(),
                        ])->addable(false)->deletable(false) 
                        ->label('Add Expense')->columnSpanFull(),  

                    Forms\Components\Hidden::make('registered_by')
                        ->default(auth()->id())
                        ->required(),
                                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fromDestination.iata_code')
                    ->label('From Destination')
                    ->size('sm')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('toDestination.iata_code')
                    ->size('sm')
                    ->label('To Destination')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('trip_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money()
                    ->searchable(),
                Tables\Columns\TextColumn::make('airline_cost')
                     ->money()
                    ->searchable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->badge()
                    ->colors([
                        'credit_card' => 'blue',
                        'cash' => 'green',
                        'bank_transfer' => 'yellow',
                        'paypal' => 'purple',
                    ])
                    ->searchable(),

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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
