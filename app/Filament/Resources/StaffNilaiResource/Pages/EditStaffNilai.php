<?php

namespace App\Filament\Resources\StaffNilaiResource\Pages;

use App\Filament\Resources\StaffNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffNilai extends EditRecord
{
    protected static string $resource = StaffNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
