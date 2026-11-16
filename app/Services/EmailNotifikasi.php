<?php

namespace App\Services;

use App\Listeners\CatatEmailKeluar;
use App\Models\EmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Kirim email notifikasi alur surat (hanya bila MAIL_NOTIFIKASI_SURAT=true) tanpa menggagalkan aksi yang sudah tersimpan.
 * Bila SMTP menolak (umumnya kuota harian hosting habis), error dicatat ke log
 * dan pengguna diberi peringatan agar tidak mengulang persetujuan.
 */
class EmailNotifikasi
{
    public const PESAN_GAGAL = 'Surat sudah diproses, tetapi notifikasi email ke pemohon gagal terkirim (batas pengiriman email harian tercapai). Tidak perlu mengulang aksi.';

    public static function kirim($penerima, Mailable $mailable): void
    {
        if (! config('mail.notifikasi_surat')) {
            // Dicatat agar dashboard bisa menunjukkan berapa kuota yang dihemat.
            EmailLog::catat([
                'kategori' => 'notifikasi_surat',
                'status' => 'dilewati',
                'subjek' => class_basename($mailable),
                'penerima' => mb_substr(is_string($penerima) ? $penerima : (string) json_encode($penerima), 0, 255),
            ]);

            return;
        }

        CatatEmailKeluar::$kategori = 'notifikasi_surat';

        try {
            Mail::to($penerima)->send($mailable);
        } catch (TransportExceptionInterface $e) {
            report($e);
            session()->flash('deleted', self::PESAN_GAGAL);
        } finally {
            CatatEmailKeluar::$kategori = null;
        }
    }
}
