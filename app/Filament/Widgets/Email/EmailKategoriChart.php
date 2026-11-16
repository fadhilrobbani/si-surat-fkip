<?php

namespace App\Filament\Widgets\Email;

use App\Models\EmailLog;
use Filament\Widgets\ChartWidget;

class EmailKategoriChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Jenis email terkirim (30 hari)';

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $data = EmailLog::selectRaw('kategori, COUNT(*) as jml')
            ->where('created_at', '>=', today()->subDays(29))->where('status', 'terkirim')
            ->groupBy('kategori')->orderByDesc('jml')->pluck('jml', 'kategori');

        return [
            'datasets' => [[
                'data' => $data->values()->all(),
                'backgroundColor' => ['#0d9488', '#6366f1', '#f59e0b', '#94a3b8'],
            ]],
            'labels' => $data->keys()->map(fn ($k) => EmailLog::KATEGORI[$k] ?? $k)->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
