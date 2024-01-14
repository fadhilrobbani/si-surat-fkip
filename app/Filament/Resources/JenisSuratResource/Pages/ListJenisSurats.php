<?php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\JenisSuratResource;
use App\Filament\Resources\JenisSuratResource\Widgets\WarningAction;

class ListJenisSurats extends ListRecords
{
    protected static string $resource = JenisSuratResource::class;

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
