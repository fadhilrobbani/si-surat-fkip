<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimLegalisir extends User
{
    public $table = 'users';
    use HasFactory;
}
