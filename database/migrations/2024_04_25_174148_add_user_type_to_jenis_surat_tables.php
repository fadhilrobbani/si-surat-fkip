<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jenis_surat_tables', function (Blueprint $table) {
            $table->enum('user_type', ['mahasiswa', 'staff'])->nullable()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surat_tables', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
};
