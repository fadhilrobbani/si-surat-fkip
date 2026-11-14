<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penanda klien pihak pertama yang tidak perlu layar persetujuan (consent).
     * Default false: klien lama tetap menampilkan consent seperti sebelumnya.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->boolean('skip_consent')->default(false)->after('require_email_verified');
        });
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('skip_consent');
        });
    }
};
