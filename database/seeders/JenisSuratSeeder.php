<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarJenisSurat = [
            [
                'name' => 'Surat Keterangan Aktif Kuliah'
            ],
            [
                'name' => 'Surat Permohonan Izin Cuti Akademik'
            ],
            [
                'name' => 'Surat Permohonan Seminar Proposal / Seminar Hasil / Ujian Komprehensif / Ujian Skripsi'
            ],
            [
                'name' => 'Surat Aktif Kembali Setelah Non-aktif'
            ],
            [
                'name' => 'Surat Permohonan Perbaikan Data PDDikti'
            ],
            [
                'name' => 'Surat Keterangan Alumni'
            ],
            [
                'name' => 'Surat Keterangan Pernah Kuliah'
            ],
            [
                'name' => 'Surat Keterangan Lulus'
            ],
            [
                'name' => 'Surat Keterangan Kesalahan Nama di Ijazah'
            ],
            [
                'name' => 'Surat Permohonan Sinkronisasi SK Homebase Dosen dengan Forlap PDDikti'
            ],
            [
                'name' => 'Surat Permohonan Izin Pra-Penelitian Mahasiswa'
            ],
            [
                'name' => 'Surat Permohonan Izin Penelitian Mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Eligible PIN'
            ],
            [
                'name' => 'Surat Usulan Bebas UKT'
            ],
        ];

        foreach ($daftarJenisSurat as $jenisSurat) {
            JenisSurat::create(['name' => $jenisSurat['name']]);
        }
    }
}
