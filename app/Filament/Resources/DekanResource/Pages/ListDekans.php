<?php

namespace App\Filament\Resources\DekanResource\Pages;

use App\Filament\Resources\DekanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDekans extends ListRecords
{
    protected static string $resource = DekanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
