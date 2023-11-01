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
            ['name' => 'D3 Bahasa Inggris', 'jurusan_id' => 3],
            ['name' => 'Pendidikan Profesi Guru (PPG)', 'jurusan_id' => 5],
            ['name' => 'S1 Bimbingan dan Konseling', 'jurusan_id' => 1],
            ['name' => 'S1 Pendidikan Bahasa Indonesia', 'jurusan_id' => 3],
            ['name' => 'S1 Pendidikan Bahasa Inggris', 'jurusan_id' => 3],
            ['name' => 'S1 Pendidikan Biologi', 'jurusan_id' => 2],
            ['name' => 'S1 Pendidikan Fisika', 'jurusan_id' => 2],
            ['name' => 'S1 Pendidikan Guru PAUD', 'jurusan_id' => 1],
            ['name' => 'S1 Pendidikan Guru Sekolah Dasar', 'jurusan_id' => 1],
            ['name' => 'S1 Pendidikan Jasmani', 'jurusan_id' => 1],
            ['name' => 'S1 Pendidikan Kimia', 'jurusan_id' => 2],
            ['name' => 'S1 Pendidikan Non Formal', 'jurusan_id' => 1],
            ['name' => 'S1 Pendidikan Matematika', 'jurusan_id' => 2],
            ['name' => 'S1 Pendidikan IPA', 'jurusan_id' => 2],
            ['name' => 'S2 Administrasi Pendidikan', 'jurusan_id' => 4],
            ['name' => 'S2 Pendidikan Bahasa Indonesia', 'jurusan_id' => 4],
            ['name' => 'S2 Pendidikan Bahasa Inggris', 'jurusan_id' => 4],
            ['name' => 'S2 Pendidikan Dasar', 'jurusan_id' => 4],
            ['name' => 'S2 Pendidikan IPA', 'jurusan_id' => 4],
            ['name' => 'S2 Pendidikan Matematika', 'jurusan_id' => 4],
            ['name' => 'S2 Teknologi Pendidikan', 'jurusan_id' => 4],
            ['name' => 'S3 Pendidikan', 'jurusan_id' => 4],
            ['name' => 'S3 Linguistik Terapan', 'jurusan_id' => 4]
        ];

        foreach ($daftarProgramStudi as $programStudi) {
            ProgramStudi::create([
                'name' => $programStudi['name'],
                'jurusan_id' => $programStudi['jurusan_id']
            ]);
        };
    }
}
