<?php

namespace App\Filament\Resources\KabagResource\Pages;

use App\Filament\Resources\KabagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKabags extends ListRecords
{
    protected static string $resource = KabagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
