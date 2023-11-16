<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
                'role_id' => 1,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()

            ],
            [
                'username' => 'mahasiswa_bk',
                'name' => 'mahasiswa_bk',
                'email' => 'mahasiswabk@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'jurusan_id' => null,
                'program_studi_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'username' => 'mahasiswa_fisika',
                'name' => 'mahasiswa_fisika',
                'email' => 'mahasiswafisika@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'mahasiswa_pgsd',
                'name' => 'mahasiswa_pgsd',
                'email' => 'mahasiswapgsd@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'jurusan_id' => null,
                'program_studi_id' => 9,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_bk',
                'name' => 'staff_bk',
                'email' => 'staffbk@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'jurusan_id' => null,
                'program_studi_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_pgsd',
                'name' => 'staff_pgsd',
                'email' => 'staffpgsd@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'jurusan_id' => null,
                'program_studi_id' => 9,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_fisika',
                'name' => 'staff_fisika',
                'email' => 'stafffisika@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_bk',
                'name' => 'kaprodi_bk',
                'email' => 'kaprodibk@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'jurusan_id' => null,
                'program_studi_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_pgsd',
                'name' => 'kaprodi_pgsd',
                'email' => 'kaprodipgsd@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'jurusan_id' => null,
                'program_studi_id' => 9,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_fisika',
                'name' => 'kaprodi_fisika',
                'email' => 'kaprodifisika@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'wd1',
                'name' => 'wd1',
                'email' => 'wd1@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 5,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_jip',
                'name' => 'akademik_jip',
                'email' => 'akademikjip@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 6,
                'jurusan_id' => 1,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_jmipa',
                'name' => 'akademik_jmipa',
                'email' => 'akademikjmipa@gmail.com',
                'password' => bcrypt('password'),
                'role_id' => 6,
                'jurusan_id' => 2,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
        ];

        foreach ($users as $user) {
            User::create(
                [
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role_id' => $user['role_id'],
                    'program_studi_id' => $user['program_studi_id'],
                    'jurusan_id' => $user['jurusan_id'],
                    'email_verified_at' => $user['email_verified_at']
                ]
            );
        };
    }
}
