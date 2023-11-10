<?php

namespace App\Filament\Resources\KaprodiResource\Pages;

use App\Filament\Resources\KaprodiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKaprodi extends EditRecord
{
    protected static string $resource = KaprodiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
