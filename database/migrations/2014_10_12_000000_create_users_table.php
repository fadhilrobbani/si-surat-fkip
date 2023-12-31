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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // $table->string('tandatangan')->nullable();
            $table->string('nip')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('program_studi_id')->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('program_studi_id')->references('id')->on('program_studi_tables')->cascadeOnDelete();
            $table->foreign('jurusan_id')->references('id')->on('jurusan_tables')->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
