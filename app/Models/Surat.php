<?php

namespace App\Models;

use App\Models\User;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Surat extends Model
{
    use HasFactory;
    protected $table = 'surat_tables';

    public function pengaju(){
        return $this->belongsTo(User::class,'pengaju_id','id');
    }

    public function current_user(){
        return $this->belongsTo(User::class,'current_user_id','id');
    }

    public function penerima(){
        return $this->belongsTo(User::class,'penerima_id','id');
    }

    public function jenisSurat(){
        return $this->belongsTo(JenisSurat::class,'jenis_surat_id','id');
    }

    public function approvals(){
        return $this->hasMany(Approval::class,'surat_id','id');
    }
}
