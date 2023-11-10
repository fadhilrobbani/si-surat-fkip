<?php

namespace App\Filament\Resources\MahasiswaResource\Pages;

use App\Models\User;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MahasiswaResource;

class CreateMahasiswa extends CreateRecord
{
    protected static string $resource = MahasiswaResource::class;
}
