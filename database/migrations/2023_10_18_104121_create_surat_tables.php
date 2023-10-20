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
            $table->id();
            $table->unsignedBigInteger('pengaju_id');
            $table->unsignedBigInteger('current_user_id');
            $table->unsignedBigInteger('penerima_id');
            $table->enum('status',['on_process','finished','denied']);
            $table->unsignedBigInteger('jenis_surat_id');
            $table->json('data')->nullable();
            $table->foreign('pengaju_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('current_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('penerima_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jenis_surat_id')->references('id')->on('jenis_surat_tables')->onDelete('cascade');
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
