<?php

namespace App\Models;

use App\Models\Surat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisSurat extends Model
{
    use HasFactory;
    protected $table = 'jenis_surat_tables';
    protected $fillable = ['name', 'slug'];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'jenis_surat_id', 'id');
    }
}
