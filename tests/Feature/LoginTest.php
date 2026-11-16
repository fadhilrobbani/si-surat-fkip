<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    private function buat(int $role, string $username, string $email): User
    {
        return User::factory()->create([
            'role_id' => $role, 'username' => $username, 'email' => $email, 'password' => bcrypt('rahasia123'),
        ]);
    }

    public function test_mahasiswa_login_dengan_email(): void
    {
        $this->buat(User::ROLE_MAHASISWA, 'A1X000001', 'mhs-uji@example.test');

        $this->post('/login', ['username' => 'mhs-uji@example.test', 'password' => 'rahasia123'])
            ->assertRedirect('/mahasiswa');
        $this->assertAuthenticated();
    }

    public function test_mahasiswa_login_dengan_npm_ditolak_dengan_pesan_jelas(): void
    {
        $this->buat(User::ROLE_MAHASISWA, 'A1X000002', 'mhs-uji2@example.test');

        $this->from('/')->post('/login', ['username' => 'A1X000002', 'password' => 'rahasia123'])
            ->assertRedirect('/')
            ->assertSessionHasErrors(['username' => 'Mahasiswa wajib login menggunakan email yang didaftarkan, bukan NPM.']);
        $this->assertGuest();
    }

    public function test_staff_login_dengan_username(): void
    {
        $this->buat(3, 'staff-uji', 'staff-uji@example.test');

        $this->post('/login', ['username' => 'staff-uji', 'password' => 'rahasia123'])->assertRedirect('/staff');
    }

    public function test_password_salah_ditolak(): void
    {
        $this->buat(3, 'staff-uji2', 'staff-uji2@example.test');

        $this->from('/')->post('/login', ['username' => 'staff-uji2', 'password' => 'salah'])
            ->assertRedirect('/')->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_admin_ditolak_di_login_web(): void
    {
        $this->buat(1, 'admin-uji2', 'admin-uji2@example.test');

        $this->from('/')->post('/login', ['username' => 'admin-uji2', 'password' => 'rahasia123'])
            ->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_home_kabag_dan_akademik_fakultas_tidak_loop(): void
    {
        $this->actingAs($this->buat(17, 'kabag-uji', 'kabag-uji@example.test'))
            ->get('/home')->assertRedirect('/kabag');
        $this->actingAs($this->buat(16, 'akfak-uji', 'akfak-uji@example.test'))
            ->get('/home')->assertRedirect('/akademik-fakultas');
    }

    public function test_token_kedaluwarsa_kembali_ke_login(): void
    {
        $request = \Illuminate\Http\Request::create('/login', 'POST', ['username' => 'x', 'password' => 'y']);
        $request->setLaravelSession($this->app['session.store']);
        $request->setRouteResolver(fn () => $this->app['router']->getRoutes()->match($request));

        $response = $this->app[\Illuminate\Contracts\Debug\ExceptionHandler::class]
            ->render($request, new TokenMismatchException());

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('login'), $response->headers->get('Location'));
    }
}
