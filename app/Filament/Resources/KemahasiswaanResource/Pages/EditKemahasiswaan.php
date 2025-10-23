<?php

namespace App\Filament\Resources\KemahasiswaanResource\Pages;

use App\Filament\Resources\KemahasiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKemahasiswaan extends EditRecord
{
    protected static string $resource = KemahasiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}