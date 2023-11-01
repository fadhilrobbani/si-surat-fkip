<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\JenisSurat;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            JurusanSeeder::class,
            ProgramStudiSeeder::class,
            UserSeeder::class,
            JenisSuratSeeder::class,
        ]);

    }
}
