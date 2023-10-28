<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApprovalPolicy
{

    public function staffCanShowRiwayatPersetujuan(User $user, Approval $approval): bool{
        return $user->id === $approval->user->id;
    }
    public function kaprodiCanShowRiwayatPersetujuan(User $user, Approval $approval): bool{
        return $user->id === $approval->user->id;
    }

}
