<?php

namespace App\Filament\Resources\KaprodiResource\Pages;

use App\Filament\Resources\KaprodiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKaprodis extends ListRecords
{
    protected static string $resource = KaprodiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
