<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\JenisSurat;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $roles = ['admin','mahasiswa','staff','kaprodi','wd1'];
        foreach($roles as $role){
            Role::create([
                'name'=>$role
            ]);
        };

        // $daftarProgramStudi = [
        //     'D3 Bahasa Inggris','Pendidikan Profesi Guru (PPG)','S1 Bimbingan dan Konseling','S1 Pendidikan Bahasa Indonesia',
        //     'S1 Pendidikan Bahasa Inggris','S1 Pendidikan Biologi','S1 Pendidikan Fisika','S1 Pendidikan Guru PAUD',
        //     'S1 Pendidikan Guru Sekolah Dasar', 'S1 Pendidikan Jasmani','S1 Pendidikan Kimia','S1 Pendidikan Non Formal',
        //     'S1 Pendidikan Matematika', 'S1 Pendidikan IPA','S2 Administrasi Pendidikan','S2 Pendidikan Bahasa Indonesia',
        //     'S2 Pendidikan Bahasa Inggris','S2 Pendidikan Dasar','S2 Pendidikan IPA','S2 Pendidikan Matematika', "S2 Teknologi Pendidikan",
        //     'S3 Pendidikan', 'S3 Linguistik Terapan'
        // ];

        $daftarJurusan = ['Jurusan Ilmu Pendidikan', 'Jurusan Pendidikan MIPA','Jurusan Pendidikan Bahasa dan Seni', 'Pascasarjana','Pendidikan Profesi Guru'];

        foreach ($daftarJurusan as $jurusan) {
            Jurusan::create([
                'name' => $jurusan,
            ]);
        }

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

        foreach($daftarProgramStudi as $programStudi){
            ProgramStudi::create([
                'name'=>$programStudi['name'],
                'jurusan_id'=> $programStudi['jurusan_id']
            ]);
        };
        $users = [
            [
                'username' => 'admin',
                'name' => 'admin',
                'email' => 'norepyl@gmail.com',
                'password'=> bcrypt('admin'),
                'role_id' => 1,
                'program_studi_id' => null,

            ],
            [
                'username' => 'G1A020036',
                'name' => 'mahasiswa1',
                'email' => 'norepyl2@gmail.com',
                'password'=> bcrypt('password'),
                'role_id' => 2,
                'program_studi_id' => 3,
            ],
            [
                'username' => 'idstaff',
                'name' => 'staff',
                'email' => 'norepyl3@gmail.com',
                'password'=> bcrypt('password'),
                'role_id' => 3,
                'program_studi_id' => null,
            ],
            [
                'username' => 'idkaprodi',
                'name' => 'kaprodi1',
                'email' => 'norepy4@gmail.com',
                'password'=> bcrypt('password'),
                'role_id'=>4,
                'program_studi_id' => 3,
            ],
            [
                'username' => 'idwd1',
                'name' => 'wd1',
                'email' => 'norepy5@gmail.com',
                'password'=> bcrypt('password'),
                'role_id'=>5,
                'program_studi_id' => null,
            ],
        ];

        foreach($users as $user){
            User::create(
                [
                    'username' => $user['username'],
                    'name'=> $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role_id' => $user['role_id'],
                    'program_studi_id' => $user['program_studi_id'],
                ]
                );
        };

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
