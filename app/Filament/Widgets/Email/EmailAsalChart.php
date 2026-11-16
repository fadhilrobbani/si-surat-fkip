<?php

namespace App\Filament\Widgets\Email;

use App\Models\EmailLog;
use App\Models\Role;
use Filament\Widgets\ChartWidget;

/** Dari mana email paling banyak dipicu: per role atau per halaman (route). */
class EmailAsalChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Asal pemicu email (30 hari)';

    protected static ?string $maxHeight = '260px';

    public ?string $filter = 'role';

    protected function getFilters(): ?array
    {
        return ['role' => 'Per role pengguna', 'route' => 'Per halaman/aksi'];
    }

    protected function getData(): array
    {
        $kolom = $this->filter === 'route' ? 'route' : 'role_id';

        $data = EmailLog::selectRaw("{$kolom} as asal, COUNT(*) as jml")
            ->where('created_at', '>=', today()->subDays(29))->whereIn('status', ['terkirim', 'gagal'])
            ->groupBy('asal')->orderByDesc('jml')->limit(10)->pluck('jml', 'asal');

        $roles = $kolom === 'role_id' ? Role::pluck('description', 'id') : collect();
        $label = fn ($asal) => $kolom === 'role_id'
            ? ($asal ? ($roles[$asal] ?? "Role {$asal}") : 'Tamu (belum login)')
            : ($asal ?: '-');

        return [
            'datasets' => [['label' => 'Email', 'data' => $data->values()->all(), 'backgroundColor' => '#0d9488']],
            'labels' => $data->keys()->map($label)->all(),
        ];
    }

    protected function getOptions(): array
    {
        return ['indexAxis' => 'y', 'plugins' => ['legend' => ['display' => false]]];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
