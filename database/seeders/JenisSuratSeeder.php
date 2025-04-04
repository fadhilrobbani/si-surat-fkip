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
                'slug' => 'surat-aktif-kuliah',
                'user_type' => 'mahasiswa'
            ],

            [
                'name' => 'Surat Rekomendasi MBKM',
                'slug' => 'surat-rekomendasi-mbkm',
                'user_type' => 'mahasiswa'
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
                'slug' => 'surat-keterangan-alumni',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Pernah Kuliah',
                'slug' => 'surat-keterangan-pernah-kuliah',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Lulus',
                'slug' => 'surat-keterangan-lulus',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Kesalahan di Ijazah',
                'slug' => 'surat-keterangan-kesalahan-ijazah',
                'user_type' => 'mahasiswa'
            ],
            // [
            //     'name' => 'Surat Permohonan Sinkronisasi SK Homebase Dosen dengan Forlap PDDikti',
            //     'slug' => 'surat-permohonan-sinkronisasi-skdosen-forlapdikti'
            // ],
            [
                'name' => 'Surat Permohonan Izin Prapenelitian Mahasiswa',
                'slug' => 'surat-permohonan-izin-prapenelitian-mahasiswa',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Permohonan Izin Penelitian Mahasiswa',
                'slug' => 'surat-permohonan-izin-penelitian-mahasiswa',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Pengantar Pembayaran Uang Yudisium',
                'slug' => 'surat-pengantar-pembayaran-uang-yudisium',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Surat Keterangan Eligible PIN',
                'slug' => 'surat-keterangan-eligible-pin',
                'user_type' => 'mahasiswa'
            ],
            [
                'name' => 'Berita Acara Nilai',
                'slug' => 'berita-acara-nilai',
                'user_type' => 'staff'
            ],
            [
                'name' => 'Surat Tugas (Individu)',
                'slug' => 'surat-tugas',
                'user_type' => 'staff'
            ],
            [
                'name' => 'Surat Tugas (Kelompok)',
                'slug' => 'surat-tugas-kelompok',
                'user_type' => 'staff'
            ],
            [
                'name' => 'Surat Keluar',
                'slug' => 'surat-keluar',
                'user_type' => 'staff-dekan'
            ],
            [
                'name' => 'Legalisir Ijazah',
                'slug' => 'legalisir-ijazah',
                'user_type' => 'mahasiswa'
            ],

        ];

        foreach ($daftarJenisSurat as $jenisSurat) {
            JenisSurat::create([
                'name' => $jenisSurat['name'],
                'slug' => $jenisSurat['slug'],
                'user_type' => $jenisSurat['user_type']
            ]);
        }
    }
}
