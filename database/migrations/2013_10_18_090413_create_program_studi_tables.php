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
        Schema::create('program_studi_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('jurusan_id');
            $table->char('kode', 3);
            $table->foreign('jurusan_id')->references('id')->on('jurusan_tables')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_studi_tables');
    }
};
