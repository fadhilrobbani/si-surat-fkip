<?php

namespace App\Filament\Resources\StaffDekanResource\Pages;

use App\Filament\Resources\StaffDekanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffDekans extends ListRecords
{
    protected static string $resource = StaffDekanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
