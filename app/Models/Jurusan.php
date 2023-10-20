<?php

namespace App\Models;

use App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurusan extends Model
{
    use HasFactory;
    public $table = 'jurusan_tables';
    protected $fillable = ['name'];

    public function programStudi(){
        return $this->hasMany(ProgramStudi::class,'jurusan_id','id');
    }
}
