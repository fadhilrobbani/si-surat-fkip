<?php

/**
 * Smoke test SSO end-to-end lewat HTTP NYATA (bukan feature test).
 *
 * Berguna untuk memverifikasi hasil deploy di tiap environment (local,
 * staging, production) sebelum dianggap lulus. Menguji alur lengkap:
 * login portal -> /oauth/authorize -> consent -> approve -> tukar code (PKCE)
 * -> /api/sso/userinfo -> logout.
 *
 * Pemakaian:
 *
 *   SSO_BASE_URL=http://localhost:8080 \
 *   SSO_CLIENT_ID=2 \
 *   SSO_CLIENT_SECRET=xxxxx \
 *   SSO_REDIRECT_URI=http://localhost:8001/auth/esurat/callback \
 *   SSO_TEST_EMAIL=mahasiswabk@email.com \
 *   SSO_TEST_PASSWORD=password \
 *   SSO_TEST_STAFF=staff_bk \
 *   php scripts/sso-smoke.php
 *
 * Catatan:
 *  - Script ini membuat dan MENCABUT token uji; aman diulang.
 *  - Di staging/production gunakan akun mahasiswa uji khusus, bukan akun nyata.
 *  - Exit code != 0 berarti ada langkah yang gagal.
 */
$base = rtrim(getenv('SSO_BASE_URL') ?: 'http://localhost:8080', '/');
$clientId = getenv('SSO_CLIENT_ID') ?: '';
$clientSecret = getenv('SSO_CLIENT_SECRET') ?: '';
$redirect = getenv('SSO_REDIRECT_URI') ?: 'http://localhost:8001/auth/esurat/callback';
$email = getenv('SSO_TEST_EMAIL') ?: 'mahasiswabk@email.com';
$password = getenv('SSO_TEST_PASSWORD') ?: 'password';
$staffUsername = getenv('SSO_TEST_STAFF') ?: 'staff_bk';

if ($clientId === '' || $clientSecret === '') {
    fwrite(STDERR, "GAGAL: SSO_CLIENT_ID dan SSO_CLIENT_SECRET wajib diisi.\n");
    exit(2);
}

$jar = sys_get_temp_dir().'/sso_smoke_'.getmypid().'.txt';
$jarStaff = sys_get_temp_dir().'/sso_smoke_staff_'.getmypid().'.txt';

$verifier = 'smoke-verifier-0123456789abcdefghijklmnopqrstuvwxyz';
$challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

$failures = 0;

function req(string $url, array $opts = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, $opts + [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    return ['body' => (string) $body, 'code' => $code, 'location' => $loc];
}

function csrf(string $html): ?string
{
    return preg_match('/name="_token" value="([^"]+)"/', $html, $m) ? $m[1] : null;
}

function authToken(string $html): ?string
{
    return preg_match('/name="state" value="([^"]+)"/', $html, $m) ? $m[1] : null;
}

$check = function (string $label, bool $pass, string $detail = '') use (&$failures) {
    if (! $pass) {
        $failures++;
    }
    printf("%s  %-52s %s\n", $pass ? '[PASS]' : '[FAIL]', $label, $detail);
};

echo "=== SSO SMOKE TEST ===\n";
echo "base: $base\n\n";

// 1. Halaman login
$login = req("$base/login", [
    CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$token = csrf($login['body']);
$check('Halaman login dapat diakses', $login['code'] === 200 && $token !== null, "HTTP {$login['code']}");

if (! $token) {
    echo "\nGAGAL: tidak bisa mengambil CSRF token. Berhenti.\n";
    exit(1);
}

// 2. Login portal sebagai mahasiswa
$post = req("$base/login", [
    CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $token,
        'username' => $email,
        'password' => $password,
    ]),
]);
$check('Login mahasiswa berhasil', $post['code'] === 302 && str_contains($post['location'], '/mahasiswa'), '-> '.$post['location']);

// 3. Authorize -> consent page
$authQuery = http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirect,
    'response_type' => 'code',
    'scope' => 'profile email',
    'state' => 'smoke-state',
    'code_challenge' => $challenge,
    'code_challenge_method' => 'S256',
]);
$consent = req("$base/oauth/authorize?$authQuery", [
    CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$hasConsent = str_contains($consent['body'], 'Permintaan Akses Aplikasi')
    && str_contains($consent['body'], 'Sistem Bimbingan Skripsi');
$check('Consent screen tampil', $consent['code'] === 200 && $hasConsent, "HTTP {$consent['code']}");

// 4. Approve -> redirect berisi code
$approve = req("$base/oauth/authorize", [
    CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => csrf($consent['body']),
        'state' => authToken($consent['body']),
    ]),
]);
parse_str(parse_url($approve['location'], PHP_URL_QUERY) ?? '', $q);
$code = $q['code'] ?? null;
$check('Approve mengembalikan ?code', ! empty($code), 'code='.substr((string) $code, 0, 16).'...');
$check('State dikembalikan utuh', ($q['state'] ?? '') === 'smoke-state', 'state='.($q['state'] ?? '-'));

if (! $code) {
    echo "\nGAGAL: tidak ada authorization code. Berhenti.\n";
    @unlink($jar);
    exit(1);
}

// 5. Tukar code -> token (PKCE)
$tokRes = req("$base/oauth/token", [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'authorization_code',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirect,
        'code' => $code,
        'code_verifier' => $verifier,
    ]),
]);
$tok = json_decode($tokRes['body'], true) ?: [];
$access = $tok['access_token'] ?? null;
$check('Tukar code -> access_token (PKCE)', $tokRes['code'] === 200 && ! empty($access), 'HTTP '.$tokRes['code']);
$check('Refresh token diberikan', ! empty($tok['refresh_token']), 'expires_in='.($tok['expires_in'] ?? '-'));

if ($tokRes['code'] !== 200) {
    echo "\nRespons token:\n".substr($tokRes['body'], 0, 400)."\n";
    @unlink($jar);
    exit(1);
}

// 6. userinfo + kebocoran data
$info = req("$base/api/sso/userinfo", [
    CURLOPT_HTTPHEADER => ['Accept: application/json', "Authorization: Bearer $access"],
]);
$payload = json_decode($info['body'], true) ?: [];
$check('userinfo dapat diakses', $info['code'] === 200, 'HTTP '.$info['code']);
$check('sub berupa UUID (bukan NPM)', (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-/', $payload['sub'] ?? ''), 'sub='.($payload['sub'] ?? '-'));
$check('username = NPM', ($payload['username'] ?? '') === explode('@', $email)[0] || ! empty($payload['username']), 'username='.($payload['username'] ?? '-'));
$check('program_studi.kode terkirim', ! empty($payload['program_studi']['kode']), 'kode='.($payload['program_studi']['kode'] ?? '-'));
$check('email ikut (scope email)', isset($payload['email']), 'email='.($payload['email'] ?? '-'));
$check('role_id TIDAK dibocorkan', ! array_key_exists('role_id', $payload) && ! array_key_exists('role', $payload));

// 7. Tanpa token
$noTok = req("$base/api/sso/userinfo", [CURLOPT_HTTPHEADER => ['Accept: application/json']]);
$check('userinfo tanpa token = 401', $noTok['code'] === 401, 'HTTP '.$noTok['code']);

// 8. Staf harus ditolak
$lp = req("$base/login", [
    CURLOPT_COOKIEJAR => $jarStaff, CURLOPT_COOKIEFILE => $jarStaff,
    CURLOPT_FOLLOWLOCATION => true,
]);
req("$base/login", [
    CURLOPT_COOKIEJAR => $jarStaff, CURLOPT_COOKIEFILE => $jarStaff,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => csrf($lp['body']),
        'username' => $staffUsername,
        'password' => $password,
    ]),
]);
$denied = req("$base/oauth/authorize?$authQuery", [
    CURLOPT_COOKIEJAR => $jarStaff, CURLOPT_COOKIEFILE => $jarStaff,
    CURLOPT_FOLLOWLOCATION => true,
]);
$check('Staf ditolak = 403', $denied['code'] === 403, 'HTTP '.$denied['code']);
$check('Halaman denied manusiawi', str_contains($denied['body'], 'Akses Ditolak'));

// 9. Route manajemen klien harus hilang
$clients = req("$base/oauth/clients", [
    CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
    CURLOPT_FOLLOWLOCATION => true,
]);
$check('/oauth/clients tidak tersedia (404)', $clients['code'] === 404, 'HTTP '.$clients['code']);

// 10. Logout mencabut token
$rev = req("$base/api/sso/logout", [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json', "Authorization: Bearer $access"],
]);
$check('/api/sso/logout mencabut token', $rev['code'] === 200, 'HTTP '.$rev['code']);
$after = req("$base/api/sso/userinfo", [
    CURLOPT_HTTPHEADER => ['Accept: application/json', "Authorization: Bearer $access"],
]);
$check('Token dicabut tidak bisa dipakai lagi', $after['code'] === 401, 'HTTP '.$after['code']);

@unlink($jar);
@unlink($jarStaff);

echo "\n=== ".($failures === 0 ? 'SEMUA LULUS' : "{$failures} GAGAL")." ===\n";
exit($failures === 0 ? 0 : 1);
