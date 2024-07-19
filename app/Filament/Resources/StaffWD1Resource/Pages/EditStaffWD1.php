<?php

namespace App\Filament\Resources\StaffWD1Resource\Pages;

use App\Filament\Resources\StaffWD1Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffWD1 extends EditRecord
{
    protected static string $resource = StaffWD1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
