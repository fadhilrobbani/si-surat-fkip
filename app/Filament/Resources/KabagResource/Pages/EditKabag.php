<?php

namespace App\Filament\Resources\KabagResource\Pages;

use App\Filament\Resources\KabagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKabag extends EditRecord
{
    protected static string $resource = KabagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
