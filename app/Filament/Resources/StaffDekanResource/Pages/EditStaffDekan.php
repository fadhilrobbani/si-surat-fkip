<?php

namespace App\Filament\Resources\StaffDekanResource\Pages;

use App\Filament\Resources\StaffDekanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffDekan extends EditRecord
{
    protected static string $resource = StaffDekanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
