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
        Schema::create('surat_tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pengaju_id')->nullable();
            $table->uuid('current_user_id')->nullable();
            $table->enum('status', ['diproses', 'selesai', 'ditolak']);
            $table->unsignedBigInteger('jenis_surat_id');
            $table->json('data')->nullable();
            $table->json('files')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->foreign('pengaju_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('current_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('jenis_surat_id')->references('id')->on('jenis_surat_tables');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tables');
    }
};
