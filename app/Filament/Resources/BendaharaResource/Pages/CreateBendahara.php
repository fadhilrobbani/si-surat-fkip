<?php

namespace App\Filament\Resources\BendaharaResource\Pages;

use App\Filament\Resources\BendaharaResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBendahara extends CreateRecord
{
    protected static string $resource = BendaharaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role_id'] = User::ROLE_BENDAHARA;

        return $data;
    }
}
