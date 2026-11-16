<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Catatan setiap email yang dicoba dikirim, untuk memantau kuota SMTP. */
class EmailLog extends Model
{
    public const KATEGORI = [
        'reset_password' => 'Reset Password',
        'verifikasi_email' => 'Verifikasi Email',
        'notifikasi_surat' => 'Notifikasi Surat',
        'lainnya' => 'Lainnya',
    ];

    public const STATUS = [
        'terkirim' => 'Terkirim',
        'gagal' => 'Gagal',
        'dilewati' => 'Dilewati (notifikasi mati)',
    ];

    protected $fillable = ['kategori', 'status', 'subjek', 'penerima', 'route', 'user_id', 'role_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** Konteks request saat ini (asal pemicu & pengguna). */
    public static function konteks(): array
    {
        $request = app()->runningInConsole() ? null : request();
        $user = auth()->user();

        return [
            'route' => $request ? ($request->route()?->getName() ?? $request->path()) : 'console',
            'user_id' => $user?->id,
            'role_id' => $user?->role_id,
        ];
    }

    /** Catat tanpa pernah menggagalkan pengiriman (mis. tabel belum dimigrasi). */
    public static function catat(array $atribut): ?self
    {
        try {
            return static::create($atribut + static::konteks());
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
