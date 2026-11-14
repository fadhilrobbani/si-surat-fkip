<?php

namespace Tests\Feature;

use App\Models\OAuthClient;
use App\Models\ProgramStudi;
use App\Models\Role;
use App\Models\User;
use App\Services\Sso\ClientAccessPolicy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Test SSO mahasiswa (OAuth2 Authorization Code + PKCE).
 *
 * Sengaja memakai DatabaseTransactions (bukan RefreshDatabase) agar tidak
 * mengosongkan database `testing` yang dipakai bersama oleh test suite lain
 * (LegacySuratRegressionTest dkk mengandalkan data seed yang sudah ada).
 */
class SsoMahasiswaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        \Laravel\Passport\Passport::loadKeysFrom(base_path('tests/fixtures/passport'));
    }

    protected function makeMahasiswa(array $overrides = []): User
    {
        $prodi = ProgramStudi::query()->first();

        return User::create(array_merge([
            'username' => 'A1L0'.rand(100000, 999999),
            'name' => 'Mahasiswa SSO Test',
            'email' => 'sso'.Str::random(8).'@example.com',
            'password' => bcrypt('password'),
            'role_id' => User::ROLE_MAHASISWA,
            'program_studi_id' => $prodi?->id,
            'email_verified_at' => now(),
        ], $overrides));
    }

    protected function makeClient(array $roleIds = [2], array $overrides = []): OAuthClient
    {
        $client = OAuthClient::create(array_merge([
            'name' => 'Sistem Bimbingan Skripsi',
            'description' => 'Aplikasi uji SSO',
            'redirect' => 'https://skripsi.test/auth/callback',
            'secret' => null,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'is_active' => true,
            'require_email_verified' => false,
            'scopes' => ['profile', 'email'],
            'grant_types' => ['authorization_code', 'refresh_token'],
        ], $overrides));

        $client->roles()->sync($roleIds);

        return $client;
    }

    /** @test */
    public function policy_allows_only_listed_roles()
    {
        $policy = app(ClientAccessPolicy::class);
        $client = $this->makeClient([User::ROLE_MAHASISWA]);

        $mahasiswa = $this->makeMahasiswa();
        $staff = $this->makeMahasiswa(['role_id' => User::ROLE_STAFF]);

        $this->assertTrue($policy->isAllowed($mahasiswa, $client));
        $this->assertFalse($policy->isAllowed($staff, $client));
        $this->assertSame('role_not_allowed', $policy->denialReason($staff, $client));
    }

    /** @test */
    public function policy_denies_when_allowlist_is_empty()
    {
        $policy = app(ClientAccessPolicy::class);
        $client = $this->makeClient([]);
        $mahasiswa = $this->makeMahasiswa();

        $this->assertFalse($policy->isAllowed($mahasiswa, $client));
    }

    /** @test */
    public function policy_denies_inactive_or_revoked_client()
    {
        $policy = app(ClientAccessPolicy::class);
        $mahasiswa = $this->makeMahasiswa();

        $inactive = $this->makeClient([User::ROLE_MAHASISWA], ['is_active' => false]);
        $revoked = $this->makeClient([User::ROLE_MAHASISWA], ['revoked' => true]);

        $this->assertFalse($policy->isAllowed($mahasiswa, $inactive));
        $this->assertSame('inactive', $policy->denialReason($mahasiswa, $inactive));

        $this->assertFalse($policy->isAllowed($mahasiswa, $revoked));
    }

    /** @test */
    public function policy_denies_unverified_email_when_required()
    {
        $policy = app(ClientAccessPolicy::class);
        $client = $this->makeClient([User::ROLE_MAHASISWA], ['require_email_verified' => true]);
        $unverified = $this->makeMahasiswa(['email_verified_at' => null]);

        $this->assertFalse($policy->isAllowed($unverified, $client));
        $this->assertSame('email_unverified', $policy->denialReason($unverified, $client));
    }

    /** @test */
    public function authorize_endpoint_renders_consent_for_allowed_mahasiswa()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA]);
        $mahasiswa = $this->makeMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(
            '/oauth/authorize?'.http_build_query([
                'client_id' => $client->getKey(),
                'redirect_uri' => $client->redirect,
                'response_type' => 'code',
                'scope' => 'profile',
                'state' => 'test-state',
                'code_challenge' => $this->codeChallenge(),
                'code_challenge_method' => 'S256',
            ])
        );

        $response->assertStatus(200);
        $response->assertSee('Sistem Bimbingan Skripsi');
        $response->assertSee('Mahasiswa SSO Test');
    }

    /** Menghasilkan code_challenge S256 untuk client publik (PKCE wajib). */
    protected function codeChallenge(string $verifier = 'test-verifier-0123456789abcdefghijklmnopqrstuvwxyz'): string
    {
        $hash = hash('sha256', $verifier, true);

        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /** @test */
    public function authorize_endpoint_denies_non_allowed_role()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA]);
        $staff = $this->makeMahasiswa(['role_id' => User::ROLE_STAFF]);

        $response = $this->actingAs($staff)->get(
            '/oauth/authorize?'.http_build_query([
                'client_id' => $client->getKey(),
                'redirect_uri' => $client->redirect,
                'response_type' => 'code',
                'scope' => 'profile',
                'state' => 'test-state',
            ])
        );

        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
        $response->assertSee('Sistem Bimbingan Skripsi');
    }

    /** @test */
    public function authorize_endpoint_denies_inactive_client()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA], ['is_active' => false]);
        $mahasiswa = $this->makeMahasiswa();

        $response = $this->actingAs($mahasiswa)->get(
            '/oauth/authorize?'.http_build_query([
                'client_id' => $client->getKey(),
                'redirect_uri' => $client->redirect,
                'response_type' => 'code',
                'scope' => 'profile',
                'state' => 'test-state',
            ])
        );

        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
    }

    /** @test */
    public function userinfo_requires_authentication()
    {
        $this->getJson('/api/sso/userinfo')->assertStatus(401);
    }

    /** @test */
    public function userinfo_returns_uuid_sub_and_prodi_kode()
    {
        $mahasiswa = $this->makeMahasiswa();

        \Laravel\Passport\Passport::actingAs($mahasiswa, ['profile']);

        $response = $this->getJson('/api/sso/userinfo')->assertStatus(200);

        $response->assertJson([
            'sub' => $mahasiswa->getKey(),
            'name' => $mahasiswa->name,
            'username' => $mahasiswa->username,
        ]);

        $this->assertNotEmpty($mahasiswa->getKey());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-/',
            $response->json('sub'),
            'sub harus berupa UUID users.id'
        );
    }

    /** @test */
    public function userinfo_hides_email_without_email_scope()
    {
        $mahasiswa = $this->makeMahasiswa();

        \Laravel\Passport\Passport::actingAs($mahasiswa, ['profile']);

        $response = $this->getJson('/api/sso/userinfo')->assertStatus(200);

        $response->assertJsonMissingPath('email');
    }

    /** @test */
    public function userinfo_includes_email_with_email_scope()
    {
        $mahasiswa = $this->makeMahasiswa();

        \Laravel\Passport\Passport::actingAs($mahasiswa, ['profile', 'email']);

        $response = $this->getJson('/api/sso/userinfo')->assertStatus(200);

        $response->assertJson([
            'email' => $mahasiswa->email,
            'email_verified' => true,
        ]);
    }

    /** @test */
    public function userinfo_requires_profile_scope()
    {
        $mahasiswa = $this->makeMahasiswa();

        \Laravel\Passport\Passport::actingAs($mahasiswa, ['email']);

        $this->getJson('/api/sso/userinfo')->assertStatus(403);
    }

    /** @test */
    public function client_management_routes_are_not_registered()
    {
        $routes = collect(app('router')->getRoutes())
            ->map(fn ($route) => $route->uri())
            ->all();

        $this->assertNotContains('oauth/clients', $routes);
        $this->assertNotContains('oauth/clients/{client_id}', $routes);
        $this->assertNotContains('oauth/personal-access-tokens', $routes);
        $this->assertNotContains('oauth/tokens', $routes);
    }

    /** @test */
    public function guest_is_redirected_to_login_then_back_to_authorize()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA]);

        $verifier = 'test-verifier-0123456789abcdefghijklmnopqrstuvwxyz';
        $query = http_build_query([
            'client_id' => $client->getKey(),
            'redirect_uri' => $client->redirect,
            'response_type' => 'code',
            'scope' => 'profile',
            'state' => 'test-state',
            'code_challenge' => $this->codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ]);

        // Guest diarahkan ke halaman login dan URL authorize disimpan sebagai intended.
        $this->get('/oauth/authorize?'.$query)->assertRedirect();

        $mahasiswa = $this->makeMahasiswa(['email' => 'login.mhs@example.com']);

        // Login lewat portal memakai email untuk role mahasiswa.
        $redirect = $this->post('/login', [
            'username' => $mahasiswa->email,
            'password' => 'password',
        ])->headers->get('Location');

        $this->assertNotNull($redirect);
        $this->assertStringContainsString('/oauth/authorize?', $redirect);
        parse_str(parse_url($redirect, PHP_URL_QUERY), $redirectParams);
        parse_str($query, $originalParams);
        $this->assertEquals($originalParams, $redirectParams);
    }

    /** @test */
    public function full_pkce_flow_issues_token_and_userinfo()
    {
        $plainSecret = Str::random(40);
        $client = $this->makeClient([User::ROLE_MAHASISWA], ['secret' => $plainSecret]);
        $mahasiswa = $this->makeMahasiswa();

        $verifier = 'test-verifier-0123456789abcdefghijklmnopqrstuvwxyz';

        // 1) Setujui authorization request.
        $this->actingAs($mahasiswa)->post('/oauth/authorize', [
            'state' => 'test-state',
        ]);

        // 2) Minta kode via endpoint authorize (Passport menyimpan authRequest di session).
        $consent = $this->actingAs($mahasiswa)->get('/oauth/authorize?'.http_build_query([
            'client_id' => $client->getKey(),
            'redirect_uri' => $client->redirect,
            'response_type' => 'code',
            'scope' => 'profile',
            'state' => 'test-state',
            'code_challenge' => $this->codeChallenge($verifier),
            'code_challenge_method' => 'S256',
        ]));

        $consent->assertStatus(200);

        // 3) Tukar code dengan token memakai PKCE verifier.
        $authToken = session('authToken');
        $this->assertNotEmpty($authToken, 'authToken harus tersimpan di session');

        $approve = $this->actingAs($mahasiswa)->post('/oauth/authorize', [
            'state' => $authToken,
        ]);

        $location = $approve->headers->get('Location');
        $this->assertNotNull($location, 'approve harus redirect ke redirect_uri');

        parse_str(parse_url($location, PHP_URL_QUERY) ?? '', $params);
        $this->assertArrayHasKey('code', $params);

        $tokenResponse = $this->post('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $client->getKey(),
            'client_secret' => $plainSecret,
            'redirect_uri' => $client->redirect,
            'code' => $params['code'],
            'code_verifier' => $verifier,
        ]);

        $tokenResponse->assertStatus(200);
        $this->assertArrayHasKey('access_token', $tokenResponse->json());
    }
    protected function authorizeQuery(OAuthClient $client): string
    {
        return http_build_query([
            'client_id' => $client->getKey(),
            'redirect_uri' => $client->redirect,
            'response_type' => 'code',
            'scope' => 'profile',
            'state' => 'test-state',
            'code_challenge' => $this->codeChallenge(),
            'code_challenge_method' => 'S256',
        ]);
    }

    /** @test */
    public function first_party_client_skips_consent_screen()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA], ['skip_consent' => true]);
        $mahasiswa = $this->makeMahasiswa();

        $location = $this->actingAs($mahasiswa)
            ->get('/oauth/authorize?'.$this->authorizeQuery($client))
            ->assertRedirect()
            ->headers->get('Location');

        $this->assertStringStartsWith($client->redirect, $location);
        parse_str((string) parse_url($location, PHP_URL_QUERY), $params);
        $this->assertNotEmpty($params['code'] ?? null);
        $this->assertSame('test-state', $params['state'] ?? null);
    }

    /** @test */
    public function client_without_skip_consent_still_shows_consent_screen()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA]);
        $mahasiswa = $this->makeMahasiswa();

        $this->actingAs($mahasiswa)
            ->get('/oauth/authorize?'.$this->authorizeQuery($client))
            ->assertOk()
            ->assertViewIs('oauth.authorize');
    }

    /** @test */
    public function login_page_from_sso_uses_client_branded_view()
    {
        $client = $this->makeClient([User::ROLE_MAHASISWA]);

        $this->get('/oauth/authorize?'.$this->authorizeQuery($client))->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertViewIs('auth.sso-login')
            ->assertSee($client->name)
            ->assertSee('https://skripsi.test', false);
    }

    /** @test */
    public function regular_login_page_is_unchanged()
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('auth.login');
    }
}
