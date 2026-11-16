<?php

namespace App\Filament\Widgets\Email;

use App\Models\EmailLog;
use Filament\Widgets\ChartWidget;

class EmailHarianChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Email per hari';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '280px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return ['7' => '7 hari', '30' => '30 hari', '90' => '90 hari'];
    }

    protected function getData(): array
    {
        $hari = (int) $this->filter;
        $mulai = today()->subDays($hari - 1);

        $baris = EmailLog::selectRaw('DATE(created_at) as tgl, status, COUNT(*) as jml')
            ->where('created_at', '>=', $mulai)
            ->groupBy('tgl', 'status')->get()
            ->groupBy('status')->map(fn ($r) => $r->pluck('jml', 'tgl'));

        $tanggal = collect(range(0, $hari - 1))->map(fn ($i) => $mulai->copy()->addDays($i));
        $seri = fn (string $status) => $tanggal->map(fn ($t) => (int) ($baris[$status][$t->toDateString()] ?? 0))->all();

        $datasets = [
            ['label' => 'Terkirim', 'data' => $seri('terkirim'), 'borderColor' => '#0d9488', 'backgroundColor' => 'rgba(13,148,136,.15)', 'fill' => true, 'tension' => .3],
            ['label' => 'Gagal', 'data' => $seri('gagal'), 'borderColor' => '#dc2626', 'backgroundColor' => '#dc2626', 'tension' => .3],
            ['label' => 'Dihemat', 'data' => $seri('dilewati'), 'borderColor' => '#6366f1', 'backgroundColor' => '#6366f1', 'borderDash' => [5, 4], 'tension' => .3],
        ];

        if ($kuota = (int) config('mail.kuota_harian')) {
            $datasets[] = ['label' => 'Kuota harian', 'data' => array_fill(0, $hari, $kuota), 'borderColor' => '#f59e0b', 'borderDash' => [2, 2], 'pointRadius' => 0, 'fill' => false];
        }

        return [
            'datasets' => $datasets,
            'labels' => $tanggal->map->translatedFormat('d M')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
