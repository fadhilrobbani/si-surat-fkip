<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AkademikFakultas extends User
{
    public $table = 'users';
    use HasFactory;
}
