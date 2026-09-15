<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Surat;
use App\Models\Approval;
use Illuminate\Support\Str;
use App\Models\ProgramStudi;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;
    use HasUuids;

    const ROLE_ADMIN = 1;
    const ROLE_MAHASISWA = 2;
    const ROLE_STAFF = 3;
    const ROLE_KAPRODI = 4;
    const ROLE_WD1 = 5;
    const ROLE_AKADEMIK = 6;
    const ROLE_STAFF_NILAI = 7;
    const ROLE_DEKAN = 8;
    const ROLE_WD2 = 9;
    const ROLE_WD3 = 10;
    const ROLE_STAFF_WD1 = 11;
    const ROLE_STAFF_WD2 = 12;
    const ROLE_STAFF_WD3 = 13;
    const ROLE_STAFF_DEKAN = 14;
    const ROLE_PENGIRIM_LEGALISIR = 15;
    const ROLE_AKADEMIK_FAKULTAS = 16;
    const ROLE_KABAG = 17;
    const ROLE_KEMAHASISWAAN = 18;
    const ROLE_TATA_USAHA = 19;
    const ROLE_UNIT_KERJASAMA = 20;
    const ROLE_LAB_PMIPA = 21;
    const ROLE_BENDAHARA = 22;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $keyType = 'string';
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role_id',
        'nip',
        'program_studi_id',
        'jurusan_id',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'id' => 'string'
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role->id == 1;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
    }

    public function suratDikirim()
    {
        return $this->hasMany(Surat::class, 'pengaju_id', 'id');
    }


    public function suratDiproses()
    {
        return $this->hasMany(Surat::class, 'current_user_id', 'id');
    }

    public function suratDiterima()
    {
        return $this->hasMany(Surat::class, 'penerima_id', 'id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'user_id', 'id');
    }
}
