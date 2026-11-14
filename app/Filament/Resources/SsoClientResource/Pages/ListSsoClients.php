<?php

namespace App\Filament\Resources\SsoClientResource\Pages;

use App\Filament\Resources\SsoClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSsoClients extends ListRecords
{
    protected static string $resource = SsoClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
