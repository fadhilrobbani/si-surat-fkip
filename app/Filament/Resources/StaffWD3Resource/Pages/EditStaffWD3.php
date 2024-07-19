<?php

namespace App\Filament\Resources\StaffWD3Resource\Pages;

use App\Filament\Resources\StaffWD3Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffWD3 extends EditRecord
{
    protected static string $resource = StaffWD3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
