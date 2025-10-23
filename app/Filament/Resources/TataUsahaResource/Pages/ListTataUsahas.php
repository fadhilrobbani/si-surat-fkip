<?php

namespace App\Filament\Resources\TataUsahaResource\Pages;

use App\Filament\Resources\TataUsahaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTataUsahas extends ListRecords
{
    protected static string $resource = TataUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
