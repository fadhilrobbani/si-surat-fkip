<?php

namespace App\Filament\Resources\TataUsahaResource\Pages;

use App\Filament\Resources\TataUsahaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTataUsaha extends EditRecord
{
    protected static string $resource = TataUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
