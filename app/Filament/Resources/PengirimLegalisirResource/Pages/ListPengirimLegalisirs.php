<?php

namespace App\Filament\Resources\PengirimLegalisirResource\Pages;

use App\Filament\Resources\PengirimLegalisirResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengirimLegalisirs extends ListRecords
{
    protected static string $resource = PengirimLegalisirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
