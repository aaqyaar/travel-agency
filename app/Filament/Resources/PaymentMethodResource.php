<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod as EnumsPaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationGroup = 'Accounting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Evc'),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'cash' => EnumsPaymentMethod::Cash->label(),
                        'mmt' => EnumsPaymentMethod::MMT->label(),
                        'bank' => EnumsPaymentMethod::Bank->label(),
                        'other' => EnumsPaymentMethod::Other->label(),
                    ])
                    ->required()
                    ->placeholder('Cash'),
                
                Forms\Components\TextInput::make('balance')
                    ->label('Opening Balance')
                    ->placeholder('0.00')->default('0.00'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => EnumsPaymentMethod::from($state)->label()),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Opening Balance'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'cash' => EnumsPaymentMethod::Cash->label(),
                        'mmt' => EnumsPaymentMethod::MMT->label(),
                        'bank' => EnumsPaymentMethod::Bank->label(),
                        'other' => EnumsPaymentMethod::Other->label(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPaymentMethods::route('/'),
        ];
    }
}
