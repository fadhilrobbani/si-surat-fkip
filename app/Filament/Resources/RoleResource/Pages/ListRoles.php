<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RoleResource\Widgets\WarningAction;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WarningAction::class,
        ];
    }
}
