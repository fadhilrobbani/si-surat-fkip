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
            ['name' => 'S1 Bimbingan dan Konseling', 'jurusan_id' => 1, 'kode' => 'A1L0'],
            ['name' => 'S1 Pendidikan Bahasa Indonesia', 'jurusan_id' => 3, 'kode' => 'A1A0'],
            ['name' => 'S1 Pendidikan Bahasa Inggris', 'jurusan_id' => 3, 'kode' => 'A1B0'],
            ['name' => 'S1 Pendidikan Biologi', 'jurusan_id' => 2, 'kode' => 'A1D0'],
            ['name' => 'S1 Pendidikan Fisika', 'jurusan_id' => 2, 'kode' => 'A1E0'],
            ['name' => 'S1 Pendidikan Guru PAUD', 'jurusan_id' => 1, 'kode' => 'A1I0'],
            ['name' => 'S1 Pendidikan Guru Sekolah Dasar', 'jurusan_id' => 1, 'kode' => 'A1G0'],
            ['name' => 'S1 Pendidikan Jasmani', 'jurusan_id' => 1, 'kode' => 'A1H0'],
            ['name' => 'S1 Pendidikan Kimia', 'jurusan_id' => 2, 'kode' => 'A1F0'],
            ['name' => 'S1 Pendidikan Non Formal', 'jurusan_id' => 1, 'kode' => 'A1J0'],
            ['name' => 'S1 Pendidikan Matematika', 'jurusan_id' => 2, 'kode' => 'A1C0'],
            ['name' => 'S1 Pendidikan IPA', 'jurusan_id' => 2, 'kode' => 'A1M0'],
            ['name' => 'S2 Administrasi Pendidikan', 'jurusan_id' => 1, 'kode' => 'A2K0'],
            ['name' => 'S2 Pendidikan Bahasa Indonesia', 'jurusan_id' => 3, 'kode' => 'A2A0'],
            ['name' => 'S2 Pendidikan Bahasa Inggris', 'jurusan_id' => 3, 'kode' => 'A2B0'],
            ['name' => 'S2 Pendidikan Dasar', 'jurusan_id' => 1, 'kode' => 'A2G0'],
            ['name' => 'S2 Pendidikan IPA', 'jurusan_id' => 2, 'kode' => 'A2L0'],
            ['name' => 'S2 Pendidikan Matematika', 'jurusan_id' => 2, 'kode' => 'A2C0'],
            ['name' => 'S2 Teknologi Pendidikan', 'jurusan_id' => 1, 'kode' => 'A2M0'],
            ['name' => 'S3 Pendidikan', 'jurusan_id' => 1, 'kode' => 'A3K0'],
            ['name' => 'S3 Linguistik Terapan', 'jurusan_id' => 3, 'kode' => 'A3A0'],
            ['name' => 'Pendidikan Profesi Guru (PPG)', 'jurusan_id' => 1,'kode' => 'A1'],
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
