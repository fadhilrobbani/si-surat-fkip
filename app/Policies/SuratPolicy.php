<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratPolicy
{


    public function mahasiswaCanViewShowRiwayatPengajuanSurat(User $user, Surat $surat): bool
    {

        return $user->id === $surat->pengaju->id;
    }

    public function mahasiswaCanCancelSurat(User $user, Surat $surat): bool
    {
        return $user->id === $surat->pengaju->id;
    }

    public function mahasiswaCanPrintSurat(User $user, Surat $surat): bool
    {
        return $user->id === $surat->pengaju->id;
    }

    public function staffCanShowSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanApproveSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanShowDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function staffCanPrintSurat(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function kaprodiCanShowSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function kaprodiCanApproveSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function kaprodiCanDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function kaprodiCanShowDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function kaprodiCanPrintSurat(User $user, Surat $surat): bool
    {
        return $user->programStudi->id === $surat->pengaju->programStudi->id;
    }

    public function akademikCanShowSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }

    public function akademikCanApproveSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }

    public function akademikCanDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }

    public function akademikCanShowDenySuratMasuk(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }

    public function akademikCanShowPreviewSuratMasuk(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }

    public function akademikCanPrintSurat(User $user, Surat $surat): bool
    {
        return $user->jurusan->id === $surat->pengaju->programStudi->jurusan->id;
    }
}
