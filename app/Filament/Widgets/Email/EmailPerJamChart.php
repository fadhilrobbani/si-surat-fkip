<?php

namespace App\Filament\Widgets\Email;

use App\Models\EmailLog;
use Filament\Widgets\ChartWidget;

class EmailPerJamChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Jam sibuk pengiriman (7 hari)';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '220px';

    protected function getData(): array
    {
        $data = EmailLog::selectRaw('HOUR(created_at) as jam, COUNT(*) as jml')
            ->where('created_at', '>=', today()->subDays(6))->whereIn('status', ['terkirim', 'gagal'])
            ->groupBy('jam')->pluck('jml', 'jam');

        return [
            'datasets' => [['label' => 'Email', 'data' => collect(range(0, 23))->map(fn ($j) => (int) ($data[$j] ?? 0))->all(), 'backgroundColor' => '#6366f1']],
            'labels' => collect(range(0, 23))->map(fn ($j) => sprintf('%02d:00', $j))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return ['plugins' => ['legend' => ['display' => false]]];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
