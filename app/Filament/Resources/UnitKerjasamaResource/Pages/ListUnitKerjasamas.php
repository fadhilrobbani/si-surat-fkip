<?php

namespace App\Filament\Resources\UnitKerjasamaResource\Pages;

use App\Filament\Resources\UnitKerjasamaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitKerjasamas extends ListRecords
{
    protected static string $resource = UnitKerjasamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
