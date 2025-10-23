<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kemahasiswaan extends User
{
    public $table = 'users';
    use HasFactory;
}