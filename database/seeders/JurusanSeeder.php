<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $daftarJurusan = ['Jurusan Ilmu Pendidikan', 'Jurusan Pendidikan MIPA', 'Jurusan Pendidikan Bahasa dan Seni', 'Pascasarjana', 'Pendidikan Profesi Guru'];

        foreach ($daftarJurusan as $jurusan) {
            Jurusan::create([
                'name' => $jurusan,
            ]);
        }

    }
}
