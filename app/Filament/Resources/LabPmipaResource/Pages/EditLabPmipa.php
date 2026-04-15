<?php

namespace App\Filament\Resources\LabPmipaResource\Pages;

use App\Filament\Resources\LabPmipaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLabPmipa extends EditRecord
{
    protected static string $resource = LabPmipaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
