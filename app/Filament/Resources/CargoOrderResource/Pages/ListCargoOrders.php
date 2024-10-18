<?php

namespace App\Filament\Resources\CargoOrderResource\Pages;

use App\Filament\Resources\CargoOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCargoOrders extends ListRecords
{
    protected static string $resource = CargoOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
