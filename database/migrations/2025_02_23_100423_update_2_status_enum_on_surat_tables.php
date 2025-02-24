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
        Schema::table('surat_tables', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE surat_tables MODIFY COLUMN status ENUM('diproses', 'selesai', 'ditolak','dibatalkan', 'dikirim', 'diverifikasi','menunggu_pembayaran') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tables', function (Blueprint $table) {
            DB::statement("ALTER TABLE surat_tables MODIFY COLUMN status ENUM('diproses', 'selesai', 'ditolak', 'dikirim', 'diverifikasi') NOT NULL");
        });
    }
};
