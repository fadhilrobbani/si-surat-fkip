<?php

namespace App\Models;

use App\Models\User;
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
}
