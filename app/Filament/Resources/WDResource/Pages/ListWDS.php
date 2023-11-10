<?php

namespace App\Filament\Resources\WDResource\Pages;

use App\Filament\Resources\WDResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWDS extends ListRecords
{
    protected static string $resource = WDResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
