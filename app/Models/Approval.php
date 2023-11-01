<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Surat;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approval extends Model
{
    use HasFactory;
    use HasUuids;
    protected $fillable = ['user_id','surat_id','isApproved','note'];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function surat(){
        return $this->belongsTo(Surat::class,'surat_id','id');
    }
}
