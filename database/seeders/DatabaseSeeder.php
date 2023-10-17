<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\JenisSurat;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $roles = ['admin','mahasiswa','staff','kaprodi','wd1'];
        foreach($roles as $role){
            Role::create([
                'name'=>$role
            ]);
        };

        $users = [
            [
                'username' => 'admin',
                'name' => 'admin',
                'email' => 'norepyl@gmail.com',
                'password'=> bcrypt('admin'),
                'role_id' => 1
            ],
            [
                'username' => 'G1A020036',
                'name' => 'mahasiswa1',
                'email' => 'norepyl2@gmail.com',
                'password'=> bcrypt('password'),
                'role_id' => 2
            ],
            [
                'username' => 'idstaff',
                'name' => 'staff',
                'email' => 'norepyl3@gmail.com',
                'password'=> bcrypt('password'),
                'role_id' => 3
            ],
            [
                'username' => 'idkaprodi',
                'name' => 'kaprodi1',
                'email' => 'norepy4@gmail.com',
                'password'=> bcrypt('password'),
                'role_id'=>4
            ],
            [
                'username' => 'idwd1',
                'name' => 'wd1',
                'email' => 'norepy5@gmail.com',
                'password'=> bcrypt('password'),
                'role_id'=>5
            ],
        ];

        foreach($users as $user){
            User::create(
                [
                    'username' => $user['username'],
                    'name'=> $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role_id' => $user['role_id']
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
