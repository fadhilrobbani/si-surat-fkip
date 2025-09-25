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
                'email' => 'admin@email.com',
                'password' => bcrypt('fkipunib123'),
                'role_id' => 1,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()

            ],
            [
                'username' => 'mahasiswa_bk',
                'name' => 'Mahasiswa Bimbingan Konseling',
                'email' => 'mahasiswabk@email.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 1,
                'email_verified_at' => now()
            ],
            [
                'username' => 'mahasiswa_fisika',
                'name' => 'Mahasiswa Fisika',
                'email' => 'mahasiswafisika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 5,
                'email_verified_at' => now()
            ],
            [
                'username' => 'mahasiswa_pgsd',
                'name' => 'Mahasiswa PGSD',
                'email' => 'mahasiswapgsd@email.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_bk',
                'name' => 'Staff S1 Bimbingan Konseling',
                'email' => 'staffbk@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 1,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_bindo',
                'name' => 'Staff S1 Pendidikan Bahasa Indonesia',
                'email' => 'staffbindo@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 2,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_bing',
                'name' => 'Staff S1 Pendidikan Bahasa Inggris',
                'email' => 'staffbing@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_biologi',
                'name' => 'Staff S1 Pendidikan Biologi',
                'email' => 'staffbiologi@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 4,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_fisika',
                'name' => 'Staff S1 Pendidikan Fisika',
                'email' => 'stafffisika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 5,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_paud',
                'name' => 'Staff S1 Pendidikan PAUD',
                'email' => 'staffpaud@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 6,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_pgsd',
                'name' => 'Staff S1 PGSD',
                'email' => 'staffpgsd@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_penjas',
                'name' => 'Staff S1 Pendidikan Jasmani',
                'email' => 'staffpenjas@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 8,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_kimia',
                'name' => 'Staff S1 Pendidikan Kimia',
                'email' => 'staffkimia@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 9,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_pnf',
                'name' => 'Staff S1 Pendidikan Non Formal',
                'email' => 'staffpnf@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 10,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_matematika',
                'name' => 'Staff S1 Pendidikan Matematika',
                'email' => 'staffmatematika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 11,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_ipa',
                'name' => 'Staff S1 Pendidikan IPA',
                'email' => 'staffipa@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 12,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2ap',
                'name' => 'Staff S2 Administrasi Pendidikan',
                'email' => 'staffs2ap@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 13,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2bindo',
                'name' => 'Staff S2 Pendidikan Bahasa Indonesia',
                'email' => 'staffs2bindo@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 14,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2bing',
                'name' => 'Staff S2 Pendidikan Bahasa Inggris',
                'email' => 'staffs2bing@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 15,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2pendas',
                'name' => 'Staff S2 Pendidikan Dasar',
                'email' => 'staffs2pendas@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 16,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2ipa',
                'name' => 'Staff S2 Pendidikan IPA',
                'email' => 'staffs2ipa@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 17,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2matematika',
                'name' => 'Staff S2 Pendidikan Matematika',
                'email' => 'staffs2matematika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 18,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s2tp',
                'name' => 'Staff S2 Teknologi Pendidikan',
                'email' => 'staffs2tp@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 19,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s3pendidikan',
                'name' => 'Staff S3 Pendidikan',
                'email' => 'staffs3pendidikan@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 20,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_s3lt',
                'name' => 'Staff S3 Linguistik Terapan',
                'email' => 'staffs3lt@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 21,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_ppg',
                'name' => 'Staff Program Profesi Guru',
                'email' => 'staffppg@email.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 22,
                'email_verified_at' => now()
            ],

            [
                'username' => 'kaprodi_bk',
                'name' => 'Kaprodi S1 Bimbingan Konseling',
                'email' => 'kaprodibk@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 1,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_bindo',
                'name' => 'Kaprodi S1 Pendidikan Bahasa Indonesia',
                'email' => 'kaprodibindo@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 2,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_bing',
                'name' => 'Kaprodi S1 Pendidikan Bahasa Inggris',
                'email' => 'kaprodibing@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_biologi',
                'name' => 'Kaprodi S1 Pendidikan Biologi',
                'email' => 'kaprodibiologi@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 4,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_fisika',
                'name' => 'Kaprodi S1 Pendidikan Fisika',
                'email' => 'kaprodifisika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 5,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_paud',
                'name' => 'Kaprodi S1 Pendidikan PAUD',
                'email' => 'kaprodipaud@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 6,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_pgsd',
                'name' => 'Kaprodi S1 PGSD',
                'email' => 'kaprodipgsd@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 7,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_penjas',
                'name' => 'Kaprodi S1 Pendidikan Jasmani',
                'email' => 'kaprodipenjas@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 8,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_kimia',
                'name' => 'Kaprodi S1 Pendidikan Kimia',
                'email' => 'kaprodikimia@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 9,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_pnf',
                'name' => 'Kaprodi S1 Pendidikan Non Formal',
                'email' => 'kaprodipnf@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 10,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_matematika',
                'name' => 'Kaprodi S1 Pendidikan Matematika',
                'email' => 'kaprodimatematika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 11,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_ipa',
                'name' => 'Kaprodi S1 Pendidikan IPA',
                'email' => 'kaprodiipa@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 12,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2ap',
                'name' => 'Kaprodi S2 Administrasi Pendidikan',
                'email' => 'kaprodis2ap@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 13,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2bindo',
                'name' => 'Kaprodi S2 Pendidikan Bahasa Indonesia',
                'email' => 'kaprodis2bindo@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 14,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2bing',
                'name' => 'Kaprodi S2 Pendidikan Bahasa Inggris',
                'email' => 'kaprodis2bing@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 15,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2pendas',
                'name' => 'Kaprodi S2 Pendidikan Dasar',
                'email' => 'kaprodis2pendas@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 16,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2ipa',
                'name' => 'Kaprodi S2 Pendidikan IPA',
                'email' => 'kaprodis2ipa@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 17,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2matematika',
                'name' => 'Kaprodi S2 Pendidikan Matematika',
                'email' => 'kaprodis2matematika@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 18,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s2tp',
                'name' => 'Kaprodi S2 Teknologi Pendidikan',
                'email' => 'kaprodis2tp@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 19,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s3pendidikan',
                'name' => 'Kaprodi S3 Pendidikan',
                'email' => 'kaprodis3pendidikan@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 20,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_s3lt',
                'name' => 'Kaprodi S3 Linguistik Terapan',
                'email' => 'kaprodis3lt@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 21,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kaprodi_ppg',
                'name' => 'Kaprodi Program Profesi Guru',
                'email' => 'kaprodippg@email.com',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => 22,
                'email_verified_at' => now()
            ],

            [
                'username' => 'wd1fkip',
                'name' => 'Abdul Rahman, S.Si., M.Si., Ph.D',
                'email' => 'wd1fkip@unib.ac.id',
                'password' => bcrypt('password'),
                'role_id' => 5,
                'nip' => '198108202006041006',
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_jip',
                'name' => 'Akademik Jurusan Ilmu Pendidikan',
                'email' => 'akademikjip@email.com',
                'password' => bcrypt('password'),
                'role_id' => 6,
                'nip' => null,
                'jurusan_id' => 1,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_jmipa',
                'name' => 'Akademik Jurusan MIPA',
                'email' => 'akademikjmipa@email.com',
                'password' => bcrypt('password'),
                'role_id' => 6,
                'nip' => null,
                'jurusan_id' => 2,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_jpbs',
                'name' => 'Akademik Jurusan Bahasa dan Seni',
                'email' => 'akademikjpbs@email.com',
                'password' => bcrypt('password'),
                'role_id' => 6,
                'nip' => null,
                'jurusan_id' => 3,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_nilai',
                'name' => 'Staff Nilai',
                'email' => 'staffnilai@email.com',
                'password' => bcrypt('password'),
                'role_id' => 13,
                'nip' => null,
                'jurusan_id' => 3,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'dekan',
                'name' => 'Dekan Fkip',
                'email' => 'dekanfkip@unib.ac.id',
                'password' => bcrypt('password'),
                'role_id' => 8,
                'nip' => '198108202006041008',
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'wd2fkip',
                'name' => 'Wakil Dekan Bidang Keuangan dan Umum',
                'email' => 'wd2fkip@unib.ac.id',
                'password' => bcrypt('password'),
                'role_id' => 9,
                'nip' => '196608202006041004',
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'wd3fkip',
                'name' => 'Wakil Dekan Bidang Kemahasiswaan',
                'email' => 'wd3fkip@unib.ac.id',
                'password' => bcrypt('password'),
                'role_id' => 10,
                'nip' => '196608202006041005',
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_wd1',
                'name' => 'Staff WD1',
                'email' => 'staffwd1@email.com',
                'password' => bcrypt('password'),
                'role_id' => 11,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_wd2',
                'name' => 'Staff WD2',
                'email' => 'staffwd2@email.com',
                'password' => bcrypt('password'),
                'role_id' => 12,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_wd3',
                'name' => 'Staff WD3',
                'email' => 'staffwd3@email.com',
                'password' => bcrypt('password'),
                'role_id' => 13,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'staff_dekan',
                'name' => 'Staff Dekan',
                'email' => 'staffdekan@email.com',
                'password' => bcrypt('password'),
                'role_id' => 14,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'pengirim_legalisir',
                'name' => 'Pengirim Legalisir',
                'email' => 'pengirimlegalisir@email.com',
                'password' => bcrypt('password'),
                'role_id' => 15,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'akademik_fakultas',
                'name' => 'Akademik Fakultas',
                'email' => 'akademikfakultas@email.com',
                'password' => bcrypt('password'),
                'role_id' => 16,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ],
            [
                'username' => 'kabag',
                'name' => 'Kabag (Arya Permana)',
                'email' => 'kabagfkip@email.com',
                'password' => bcrypt('password'),
                'role_id' => 17,
                'nip' => null,
                'jurusan_id' => null,
                'program_studi_id' => null,
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $user) {
            User::create(
                [
                    'username' => $user['username'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role_id' => $user['role_id'],
                    'nip' => $user['nip'],
                    'program_studi_id' => $user['program_studi_id'],
                    'jurusan_id' => $user['jurusan_id'],
                    'email_verified_at' => $user['email_verified_at']
                ]
            );
        };
    }
}
