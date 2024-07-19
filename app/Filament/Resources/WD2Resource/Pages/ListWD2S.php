<?php

namespace App\Filament\Resources\WD2Resource\Pages;

use App\Filament\Resources\WD2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWD2S extends ListRecords
{
    protected static string $resource = WD2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
