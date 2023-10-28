<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\ProgramStudi;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role_id',
        'program_studi_id',
        'jurusan_id'
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
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function programStudi(){
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id','id');
    }

    public function suratDikirim(){
        return $this->hasMany(Surat::class,'pengaju_id','id');
    }


    public function suratDiproses(){
        return $this->hasMany(Surat::class,'current_user_id','id');
    }

    public function suratDiterima(){
        return $this->hasMany(Surat::class,'penerima_id','id');
    }

    public function approvals(){
        return $this->hasMany(Approval::class,'user_id','id');
    }

    public function jurusan(){
        return $this->belongsTo(Jurusan::class,'jurusan_id','id');
    }



}

