<?php

namespace App\Filament\Resources\AkademikResource\Pages;

use App\Filament\Resources\AkademikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAkademik extends EditRecord
{
    protected static string $resource = AkademikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
