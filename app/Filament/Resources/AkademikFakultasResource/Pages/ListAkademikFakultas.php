<?php

namespace App\Filament\Resources\AkademikFakultasResource\Pages;

use App\Filament\Resources\AkademikFakultasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAkademikFakultas extends ListRecords
{
    protected static string $resource = AkademikFakultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
