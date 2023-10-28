<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratPolicy
{


    public function mahasiswaCanViewShowRiwayatPengajuanSurat(User $user, Surat $surat):bool
    {

        return $user->id === $surat->pengaju->id;
    }

    public function mahasiswaCanCancelSurat(User $user, Surat $surat):bool{
        return $user->id === $surat->pengaju->id;
    }

    public function staffCanShowSuratMasuk(User $user, Surat $surat):bool{
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanApproveSuratMasuk(User $user, Surat $surat):bool{
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanDenySuratMasuk(User $user, Surat $surat):bool{
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanShowDenySuratMasuk(User $user, Surat $surat):bool{
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }



}
