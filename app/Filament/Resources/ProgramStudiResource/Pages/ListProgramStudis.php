<?php

namespace App\Filament\Resources\ProgramStudiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProgramStudiResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\ProgramStudiResource\Widgets\ProgramStudiOverview;
use App\Filament\Resources\ProgramStudiResource\Widgets\WarningAction;

class ListProgramStudis extends ListRecords
{
    // use ExposesTableToWidgets;
    protected static string $resource = ProgramStudiResource::class;

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
