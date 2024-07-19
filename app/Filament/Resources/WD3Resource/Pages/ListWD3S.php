<?php

namespace App\Filament\Resources\WD3Resource\Pages;

use App\Filament\Resources\WD3Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWD3S extends ListRecords
{
    protected static string $resource = WD3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
