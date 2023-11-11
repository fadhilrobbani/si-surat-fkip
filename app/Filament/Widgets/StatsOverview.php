<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\ProgramStudi;
use App\Models\Surat;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Akun', User::all()->count()),
            Stat::make('Jumlah Program Studi', ProgramStudi::all()->count()),
            Stat::make('Jumlah Jurusan', Jurusan::all()->count()),
            // Stat::make('Jumlah Surat Yang Pernah Diajukan', Surat::all()->count()),
            // Stat::make('Jumlah Surat Diproses', Surat::where('status','on_process')->count()),
            // Stat::make('Jumlah Surat Ditolak', Surat::where('status','denied')->count()),
            // Stat::make('Jumlah Surat Disetujui', Surat::where('status','finished')->count()),
        ];
    }


}
