<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationGroup = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255), 
                Forms\Components\TextInput::make('continent')
                ->required()
                ->maxLength(255), 
                Forms\Components\TextInput::make('iata_code')
                ->required()
                ->maxLength(255), 
                    Forms\Components\TextInput::make('municipality')
                    ->required()
                    ->maxLength(255),   
                Country::make('iso_country')
                    ->required(),
                Forms\Components\Hidden::make('registered_by')
                    ->default(fn () => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([100])
            ->query(
                Destination::query()->with('registeredBy')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('municipality')->searchable(),
                Tables\Columns\TextColumn::make('iso_country'),
                Tables\Columns\TextColumn::make('iata_code')->searchable(),
                Tables\Columns\TextColumn::make('registeredBy.name')             
                    ->label('Registered By'),
                Tables\Columns\TextColumn::make('created_at'),
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
            'index' => Pages\ListDestinations::route('/')
        ];
    }
}
