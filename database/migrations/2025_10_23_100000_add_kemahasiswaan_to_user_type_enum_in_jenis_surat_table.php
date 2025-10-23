<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE jenis_surat_tables MODIFY COLUMN user_type ENUM('mahasiswa', 'staff', 'staff-dekan', 'akademik', 'akademik_fakultas', 'kemahasiswaan') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE jenis_surat_tables MODIFY COLUMN user_type ENUM('mahasiswa', 'staff', 'staff-dekan', 'akademik', 'akademik_fakultas') NOT NULL");
    }
};