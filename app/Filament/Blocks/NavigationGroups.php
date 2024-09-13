<?php

namespace App\Filament\Blocks;

use Filament\Navigation\NavigationGroup;

class NavigationGroups {
    public static function render(): array
    {
        return [
            NavigationGroup::make()
                    ->label('Business Partners')
                    ->icon('heroicon-o-globe-alt'),
                NavigationGroup::make()
                    ->label('Orders')
                    ->icon('heroicon-o-clipboard'),          
                NavigationGroup::make()
                    ->label('Accounting')
                    ->icon('heroicon-o-building-library'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog'),
        ];
    }
}