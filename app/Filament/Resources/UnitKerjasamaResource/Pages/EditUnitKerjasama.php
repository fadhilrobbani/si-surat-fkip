<?php

namespace App\Filament\Resources\UnitKerjasamaResource\Pages;

use App\Filament\Resources\UnitKerjasamaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitKerjasama extends EditRecord
{
    protected static string $resource = UnitKerjasamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
