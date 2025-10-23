<?php

namespace App\Filament\Resources\KemahasiswaanResource\Pages;

use App\Filament\Resources\KemahasiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKemahasiswaans extends ListRecords
{
    protected static string $resource = KemahasiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}