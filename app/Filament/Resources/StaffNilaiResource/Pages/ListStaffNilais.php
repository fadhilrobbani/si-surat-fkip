<?php

namespace App\Filament\Resources\StaffNilaiResource\Pages;

use App\Filament\Resources\StaffNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffNilais extends ListRecords
{
    protected static string $resource = StaffNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
