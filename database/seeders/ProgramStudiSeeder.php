<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarProgramStudi = [
            // ['name' => 'D3 Bahasa Inggris', 'jurusan_id' => 3],
            // ['name' => 'Pendidikan Profesi Guru (PPG)', 'jurusan_id' => 5],
            ['name' => 'S1 Bimbingan dan Konseling', 'jurusan_id' => 1, 'kode' => 'A1L'],
            ['name' => 'S1 Pendidikan Bahasa Indonesia', 'jurusan_id' => 3, 'kode' => 'A1A'],
            ['name' => 'S1 Pendidikan Bahasa Inggris', 'jurusan_id' => 3, 'kode' => 'A1B'],
            ['name' => 'S1 Pendidikan Biologi', 'jurusan_id' => 2, 'kode' => 'A1D'],
            ['name' => 'S1 Pendidikan Fisika', 'jurusan_id' => 2, 'kode' => 'A1E'],
            ['name' => 'S1 Pendidikan Guru PAUD', 'jurusan_id' => 1, 'kode' => 'A1I'],
            ['name' => 'S1 Pendidikan Guru Sekolah Dasar', 'jurusan_id' => 1, 'kode' => 'A1G'],
            ['name' => 'S1 Pendidikan Jasmani', 'jurusan_id' => 1, 'kode' => 'A1H'],
            ['name' => 'S1 Pendidikan Kimia', 'jurusan_id' => 2, 'kode' => 'A1F'],
            ['name' => 'S1 Pendidikan Non Formal', 'jurusan_id' => 1, 'kode' => 'A1J'],
            ['name' => 'S1 Pendidikan Matematika', 'jurusan_id' => 2, 'kode' => 'A1C'],
            ['name' => 'S1 Pendidikan IPA', 'jurusan_id' => 2, 'kode' => 'A1M'],
            ['name' => 'S2 Administrasi Pendidikan', 'jurusan_id' => 1, 'kode' => 'A2K'],
            ['name' => 'S2 Pendidikan Bahasa Indonesia', 'jurusan_id' => 3, 'kode' => 'A2A'],
            ['name' => 'S2 Pendidikan Bahasa Inggris', 'jurusan_id' => 3, 'kode' => 'A2B'],
            ['name' => 'S2 Pendidikan Dasar', 'jurusan_id' => 1, 'kode' => 'A2G'],
            ['name' => 'S2 Pendidikan IPA', 'jurusan_id' => 2, 'kode' => 'A2L'],
            ['name' => 'S2 Pendidikan Matematika', 'jurusan_id' => 2, 'kode' => 'A2C'],
            ['name' => 'S2 Teknologi Pendidikan', 'jurusan_id' => 1, 'kode' => 'A2M'],
            ['name' => 'S3 Pendidikan', 'jurusan_id' => 1, 'kode' => 'A3K'],
            ['name' => 'S3 Linguistik Terapan', 'jurusan_id' => 3, 'kode' => 'A3A']
        ];

        foreach ($daftarProgramStudi as $programStudi) {
            ProgramStudi::create([
                'name' => $programStudi['name'],
                'jurusan_id' => $programStudi['jurusan_id'],
                'kode' => $programStudi['kode']
            ]);
        };
    }
}
