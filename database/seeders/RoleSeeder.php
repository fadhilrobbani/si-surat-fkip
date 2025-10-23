<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            ['name' => 'admin', 'description' => 'Admin'],
            ['name' => 'mahasiswa', 'description' => 'Mahasiswa'],
            ['name' => 'staff', 'description' => 'Staff'],
            ['name' => 'kaprodi', 'description' => 'Kaprodi'],
            ['name' => 'wd', 'description' => 'Wakil Dekan Bidang Akademik'],
            ['name' => 'akademik', 'description' => 'Akademik'],
            ['name' => 'staff-nilai', 'description' => 'Staff Nilai'],
            ['name' => 'dekan', 'description' => 'Dekan'],
            ['name' => 'wd2', 'description' => 'Wakil Dekan Bidang Keuangan dan Umum'],
            ['name' => 'wd3', 'description' => 'Wakil Dekan Bidang Kemahasiswaan'],
            ['name' => 'staff-wd1', 'description' => 'Staff WD1'],
            ['name' => 'staff-wd2', 'description' => 'Staff WD2'],
            ['name' => 'staff-wd3', 'description' => 'Staff WD3'],
            ['name' => 'staff-dekan', 'description' => 'Staff Dekan'],
            ['name' => 'pengirim-legalisir', 'description' => 'Pengirim Legalisir'],
            ['name' => 'akademik-fakultas', 'description' => 'Akademik Fakultas'],
            ['name' =>'kabag', 'description' => 'Kabag'],
            ['name' => 'kemahasiswaan', 'description' => 'Kemahasiswaan'],
            ['name' => 'tata-usaha', 'description' => 'Tata Usaha'],
            ['name' => 'unit-kerjasama', 'description' => 'Unit Kerjasama'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        };
    }
}
