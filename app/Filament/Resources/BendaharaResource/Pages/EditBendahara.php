<?php

namespace App\Filament\Resources\BendaharaResource\Pages;

use App\Filament\Resources\BendaharaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBendahara extends EditRecord
{
    protected static string $resource = BendaharaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
