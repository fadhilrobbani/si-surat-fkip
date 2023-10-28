<?php

namespace App\Policies;

use App\Models\User;

class FilePolicy
{
    // /**
    //  * Create a new policy instance.
    //  */
    // public function __construct()
    // {
    //     //
    // }

    public function showFile(User $user){
        dd('halo bro '. $user->name);
        return true;
    }
}
