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
                'name' => 'Surat Keterangan Aktif Kuliah',
                'slug' => 'surat-aktif-kuliah'
            ],
            [
                'name' => 'Surat Pengantaran Pembayaran Uang Yudisium',
                'slug' => 'surat-pengantar-pembayaran-uang-yudisium'
            ],
            [
                'name' => 'Surat Rekomendasi MBKM',
                'slug' => 'surat-rekomendasi-mbkm'
            ],
            // [
            //     'name' => 'Surat Permohonan Izin Cuti Akademik',
            //     'slug' => 'surat-izin-cuti'
            // ],
            // [
            //     'name' => 'Surat Permohonan Seminar Proposal / Seminar Hasil / Ujian Komprehensif / Ujian Skripsi',
            //     'slug' => 'surat-permohonan-seminar'
            // ],
            // [
            //     'name' => 'Surat Aktif Kembali Setelah Non-aktif',
            //     'slug' => 'surat-aktif-kembali'
            // ],
            // [
            //     'name' => 'Surat Permohonan Perbaikan Data PDDikti',
            //     'slug' => 'surat-permohonan-perbaikan-data'
            // ],
            [
                'name' => 'Surat Keterangan Alumni',
                'slug' => 'surat-keterangan-alumni'

            ],
            [
                'name' => 'Surat Keterangan Pernah Kuliah',
                'slug' => 'surat-keterangan-pernah-kuliah'
            ],
            [
                'name' => 'Surat Keterangan Lulus',
                'slug' => 'surat-keterangan-lulus'
            ],
            [
                'name' => 'Surat Keterangan Kesalahan di Ijazah',
                'slug' => 'surat-keterangan-kesalahan-ijazah'
            ],
            // [
            //     'name' => 'Surat Permohonan Sinkronisasi SK Homebase Dosen dengan Forlap PDDikti',
            //     'slug' => 'surat-permohonan-sinkronisasi-skdosen-forlapdikti'
            // ],
            [
                'name' => 'Surat Permohonan Izin Prapenelitian Mahasiswa',
                'slug' => 'surat-permohonan-izin-prapenelitian-mahasiswa'
            ],
            [
                'name' => 'Surat Permohonan Izin Penelitian Mahasiswa',
                'slug' => 'surat-permohonan-izin-penelitian-mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Eligible PIN',
                'slug' => 'surat-keterangan-eligible-pin'
            ],
            // [
            //     'name' => 'Surat Usulan Bebas UKT',
            //     'slug' => 'surat-usulan-bebas-ukt'
            // ],
        ];

        foreach ($daftarJenisSurat as $jenisSurat) {
            JenisSurat::create([
                'name' => $jenisSurat['name'],
                'slug' => $jenisSurat['slug']
            ]);
        }
    }
}
