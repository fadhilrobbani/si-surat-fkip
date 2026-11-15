<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Login admin di browser yang sebelumnya dipakai akun lain (mis. mahasiswa
 * lewat SSO) tidak boleh terpental kembali ke halaman login.
 */
class AdminLoginSetelahAkunLainTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function admin_can_log_in_after_another_account_used_the_same_session()
    {
        $lain = User::factory()->create(['username' => 'mhs-uji', 'role_id' => User::ROLE_MAHASISWA, 'password' => bcrypt('rahasia-lain')]);
        $admin = User::factory()->create(['role_id' => 1, 'username' => 'admin-uji', 'password' => bcrypt('rahasia-admin')]);

        // AuthenticateSession di panel menyimpan hash password akun lama.
        $this->actingAs($lain)->get('/admin/login');
        $this->assertSame($lain->password, session('password_hash_web'));

        // Langkah yang sama dengan CustomLogin::authenticate().
        $this->assertTrue(auth()->attempt(['username' => 'admin-uji', 'password' => 'rahasia-admin']));
        session()->regenerate();

        $this->get('/admin')->assertOk();
        $this->assertAuthenticatedAs($admin);
    }
}
