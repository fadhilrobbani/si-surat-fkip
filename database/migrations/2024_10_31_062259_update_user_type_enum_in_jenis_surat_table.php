<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mengubah enumeration secara lebih rapi
        Schema::table('jenis_surat_tables', function (Blueprint $table) {
            // Menggunakan raw SQL untuk mengubah kolom enum
            DB::statement("ALTER TABLE jenis_surat_tables MODIFY user_type ENUM('mahasiswa', 'staff', 'staff-dekan')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surat', function (Blueprint $table) {
            // Mengembalikan kolom enum ke nilai sebelumnya
            DB::statement("ALTER TABLE jenis_surat MODIFY user_type ENUM('mahasiswa', 'staff')");
        });
    }
};
