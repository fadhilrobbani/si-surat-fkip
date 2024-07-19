<?php

namespace App\Filament\Resources\StaffWD2Resource\Pages;

use App\Filament\Resources\StaffWD2Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffWD2S extends ListRecords
{
    protected static string $resource = StaffWD2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
