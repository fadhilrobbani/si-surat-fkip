<?php

namespace App\Filament\Resources\StaffWD3Resource\Pages;

use App\Filament\Resources\StaffWD3Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffWD3S extends ListRecords
{
    protected static string $resource = StaffWD3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
