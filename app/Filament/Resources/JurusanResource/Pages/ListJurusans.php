<?php

namespace App\Filament\Resources\JurusanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\JurusanResource;
use App\Filament\Resources\JurusanResource\Widgets\WarningAction;

class ListJurusans extends ListRecords
{
    protected static string $resource = JurusanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WarningAction::class,
        ];
    }
}
