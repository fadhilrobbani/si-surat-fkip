<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 30);      // reset_password | verifikasi_email | notifikasi_surat | lainnya
            $table->string('status', 15);        // terkirim | gagal | dilewati
            $table->string('subjek')->nullable();
            $table->string('penerima')->nullable();
            $table->string('route')->nullable(); // asal pemicu (nama route / URL)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedTinyInteger('role_id')->nullable();
            $table->timestamps();
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
