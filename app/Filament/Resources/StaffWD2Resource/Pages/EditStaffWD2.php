<?php

namespace App\Filament\Resources\StaffWD2Resource\Pages;

use App\Filament\Resources\StaffWD2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffWD2 extends EditRecord
{
    protected static string $resource = StaffWD2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
