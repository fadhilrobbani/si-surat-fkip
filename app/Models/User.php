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
