<?php

namespace App\Filament\Resources\WD2Resource\Pages;

use App\Filament\Resources\WD2Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWD2 extends EditRecord
{
    protected static string $resource = WD2Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
