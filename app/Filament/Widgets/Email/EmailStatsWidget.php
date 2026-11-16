<?php

namespace App\Filament\Widgets\Email;

use App\Models\EmailLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $hariIni = fn (string $status) => EmailLog::whereDate('created_at', today())->where('status', $status)->count();
        $terkirim = $hariIni('terkirim');
        $gagal = $hariIni('gagal');
        $dilewati = $hariIni('dilewati');
        $kuota = (int) config('mail.kuota_harian');

        $tren = collect(range(6, 0))->map(fn ($i) => EmailLog::whereDate('created_at', today()->subDays($i))
            ->where('status', 'terkirim')->count())->all();

        $tigaPuluh = EmailLog::where('created_at', '>=', today()->subDays(29))->where('status', 'terkirim')->count();
        $puncak = EmailLog::selectRaw('DATE(created_at) as tgl, COUNT(*) as jml')
            ->where('created_at', '>=', today()->subDays(29))->where('status', 'terkirim')
            ->groupBy('tgl')->orderByDesc('jml')->first();

        if ($kuota > 0) {
            $persen = round($terkirim / $kuota * 100);
            $kuotaStat = Stat::make('Terkirim hari ini', "{$terkirim} / {$kuota}")
                ->description("{$persen}% kuota harian · sisa ".max(0, $kuota - $terkirim))
                ->color($persen >= 90 ? 'danger' : ($persen >= 70 ? 'warning' : 'success'));
        } else {
            $kuotaStat = Stat::make('Terkirim hari ini', $terkirim)
                ->description('Isi MAIL_KUOTA_HARIAN di .env untuk melihat % kuota')
                ->color('primary');
        }

        return [
            $kuotaStat->chart($tren)->descriptionIcon('heroicon-m-paper-airplane'),
            Stat::make('Gagal hari ini', $gagal)
                ->description($gagal > 0 ? 'Ditolak SMTP — kemungkinan kuota habis' : 'Tidak ada kegagalan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($gagal > 0 ? 'danger' : 'success'),
            Stat::make('Dihemat hari ini', $dilewati)
                ->description('Notifikasi surat yang tidak dikirim (fitur dimatikan)')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info'),
            Stat::make('Terkirim 30 hari', $tigaPuluh)
                ->description('Rata-rata '.round($tigaPuluh / 30, 1).'/hari'.($puncak ? " · puncak {$puncak->jml} (".\Carbon\Carbon::parse($puncak->tgl)->translatedFormat('d M').')' : ''))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray'),
        ];
    }
}
