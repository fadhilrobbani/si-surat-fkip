<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;

/**
 * Setiap email dicatat "gagal" saat mulai dikirim, lalu diubah "terkirim"
 * setelah SMTP menerimanya. Jadi email yang ditolak SMTP (kuota habis)
 * otomatis tetap tercatat gagal tanpa perlu menyentuh tiap pemanggil.
 */
class CatatEmailKeluar
{
    /** Kategori yang dipaksa pemanggil (mis. EmailNotifikasi). */
    public static ?string $kategori = null;

    /** @var array<int, int> id log yang sedang menunggu konfirmasi kirim */
    private static array $menunggu = [];

    public function sending(MessageSending $event): void
    {
        $log = EmailLog::catat([
            'kategori' => $this->kategori($event->data),
            'status' => 'gagal',
            'subjek' => mb_substr((string) $event->message->getSubject(), 0, 255),
            'penerima' => mb_substr(collect($event->message->getTo())->map->getAddress()->implode(', '), 0, 255),
        ]);

        if ($log) {
            self::$menunggu[] = $log->id;
        }
    }

    public function sent(MessageSent $event): void
    {
        if ($id = array_pop(self::$menunggu)) {
            try {
                EmailLog::whereKey($id)->update(['status' => 'terkirim']);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    private function kategori(array $data): string
    {
        return match ($data['__laravel_notification'] ?? null) {
            ResetPassword::class => 'reset_password',
            VerifyEmail::class => 'verifikasi_email',
            default => self::$kategori ?? 'lainnya',
        };
    }
}
