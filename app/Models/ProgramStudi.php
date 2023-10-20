<?php

namespace App\Models;

use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramStudi extends Model
{
    protected $table = 'program_studi_tables';
    protected $fillable = ['name'];
    use HasFactory;

    public function users(){
        return $this->hasMany(User::class,'program_studi_id', 'id');
    }

    public function jurusan(){
        return $this->belongsTo(Jurusan::class, 'jurusan_id','id');
    }
}
