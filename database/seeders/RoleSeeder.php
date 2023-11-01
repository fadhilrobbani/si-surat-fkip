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
        $roles = ['admin', 'mahasiswa', 'staff', 'kaprodi', 'wd1', 'akademik'];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        };
    }
}
