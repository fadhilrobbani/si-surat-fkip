<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom SSO pada oauth_clients dan tabel allowlist role.
     *
     * Semua kolom bersifat aditif (nullable / punya default) sehingga baris
     * klien yang sudah ada tidak terganggu.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('description');
            $table->boolean('require_email_verified')->default(true)->after('is_active');
            $table->json('scopes')->nullable()->after('require_email_verified');
            $table->json('grant_types')->nullable()->after('scopes');
        });

        Schema::create('oauth_client_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('oauth_clients')
                ->cascadeOnDelete();
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->cascadeOnDelete();

            $table->unique(['client_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_client_roles');

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'is_active',
                'require_email_verified',
                'scopes',
                'grant_types',
            ]);
        });
    }
};
