<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OAuthClient;
use Illuminate\Support\Str;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $klienSso = $this->klienSsoDariIntended($request);

        // Tanpa penanda `sso` berarti e-Surat dibuka langsung. Buang tujuan
        // /oauth/authorize yang tertinggal dari percobaan SSO sebelumnya agar
        // tampilan dan redirect setelah login kembali ke e-Surat.
        if ($klienSso === null || $request->query('sso') !== (string) $klienSso->getKey()) {
            if (str_contains((string) $request->session()->get('url.intended', ''), '/oauth/authorize')) {
                $request->session()->forget('url.intended');
            }

            $klienSso = null;
        }

        if ($klienSso !== null) {
            return view('auth.sso-login', [
                'client' => $klienSso,
                'kembaliUrl' => $this->asalRedirect($klienSso->redirect),
            ]);
        }

        return view('auth.login');
    }

    /**
     * Klien SSO aktif bila pengguna sampai di halaman login karena diarahkan
     * oleh /oauth/authorize. Login biasa e-Surat mengembalikan null.
     */
    private function klienSsoDariIntended(Request $request): ?OAuthClient
    {
        $intended = (string) $request->session()->get('url.intended', '');

        if (! str_contains($intended, '/oauth/authorize')) {
            return null;
        }

        parse_str((string) parse_url($intended, PHP_URL_QUERY), $query);
        $clientId = $query['client_id'] ?? null;

        if (! is_string($clientId) || $clientId === '') {
            return null;
        }

        $client = OAuthClient::query()->find($clientId);

        return $client && $client->is_active && ! $client->revoked ? $client : null;
    }

    /** Origin aplikasi klien (skema + host + port) untuk tautan "kembali". */
    private function asalRedirect(string $redirect): ?string
    {
        $bagian = parse_url($redirect);

        if (! isset($bagian['scheme'], $bagian['host'])) {
            return null;
        }

        return $bagian['scheme'].'://'.$bagian['host'].(isset($bagian['port']) ? ':'.$bagian['port'] : '');
    }

    public function create()
    {
        return view('auth.register', [
            'daftarProgramStudi' => ProgramStudi::all(),
        ]);
    }

    public function store(Request $request)
    {
        $programStudiKode = ProgramStudi::pluck('kode');
        $formFields = $request->validate([
            // 'username' => 'required|unique:users,username|starts_with:foo,bar',
            'username' => [
                'required',
                'size:9',
                // 'unique:users,username'
                function ($attribute, $value, $fail) use ($programStudiKode) {
                    // Gunakan callback untuk memeriksa apakah nilai diawali dengan salah satu kode program studi
                    foreach ($programStudiKode as $kode) {
                        if (strpos($value, $kode) === 0) {
                            return; // Validasi berhasil
                        }
                    }

                    // Validasi gagal jika tidak ada kode program studi yang cocok
                    $fail("NPM yang anda masukkan tidak sesuai atau tidak terdaftar dalam program studi di FKIP!");
                },
            ],
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'program-studi' => ['required'],
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'program_studi_id' => $request->input('program-studi'),
            'password' => bcrypt($request->input('password')),
            'role_id' => 2,
        ]);
        event(new Registered($user));
        auth()->login($user);
        return redirect('/')->with('success', 'Berhasil mendaftarkan akun');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('');
    }

    public function emailVerification(User $user)
    {
        $user->sendEmailVerificationNotification();
        return back()->with('success', 'Email Verifikasi telah dikirim!');
    }

    public function forgotPasswordPage()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        if (auth()->check()) {
            if (auth()->user()->email != $request->input('email')) {
                return redirect()->back()->withErrors('Email tidak sesuai dengan yang Anda Daftarkan');
            }
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordPage(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /** Prefix dashboard per role_id. Satu-satunya peta role → halaman awal. */
    private const DASHBOARD = [
        1 => '/admin',
        2 => '/mahasiswa',
        3 => '/staff',
        4 => '/kaprodi',
        5 => '/wd',
        6 => '/akademik',
        7 => '/staff-nilai',
        8 => '/dekan',
        9 => '/wd2',
        10 => '/wd3',
        11 => '/staff-wd1',
        12 => '/staff-wd2',
        13 => '/staff-wd3',
        14 => '/staff-dekan',
        15 => '/pengirim-legalisir',
        16 => '/akademik-fakultas',
        17 => '/kabag',
        18 => '/kemahasiswaan',
        19 => '/tata-usaha',
        20 => '/unit-kerjasama',
        21 => '/lab-pmipa',
        22 => '/bendahara',
    ];

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $identifier = $request->input('username');
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
        $loginType = $isEmail ? 'email' : 'username';

        $user = User::where($loginType, $identifier)->where('role_id', '!=', 1)->first();

        if (! $user) {
            return back()->withErrors(['username' => 'Username/Email salah atau tidak terdaftar'])->withInput();
        }

        // NPM bukan identifier login mahasiswa (bisa duplikat); wajib email.
        if ($user->role_id == 2 && ! $isEmail) {
            return back()->withErrors(['username' => 'Mahasiswa wajib login menggunakan email yang didaftarkan, bukan NPM.'])->withInput();
        }

        if (! auth()->attempt([$loginType => $identifier, 'password' => $request->input('password')])) {
            return back()->withErrors('Username atau password tidak sesuai, silahkan coba lagi')->withInput();
        }

        $request->session()->regenerate();

        // Jika user sampai di halaman login karena diarahkan oleh endpoint
        // SSO (/oauth/authorize), kembalikan ke sana agar mahasiswa tidak
        // perlu menekan tombol SSO dua kali.
        $intended = $request->session()->pull('url.intended');
        if ($intended && str_contains($intended, '/oauth/authorize')) {
            return redirect()->to($intended);
        }

        $tujuan = self::DASHBOARD[auth()->user()->role_id] ?? null;
        if ($tujuan === null || auth()->user()->role_id == 1) {
            auth()->logout();
            return back()->withErrors('Akun Anda tidak memiliki akses ke halaman ini.')->withInput();
        }

        return redirect($tujuan)->with('success', 'Anda berhasil login');
    }

    public function home()
    {
        if (auth()->check()) {
            $tujuan = self::DASHBOARD[auth()->user()->role_id] ?? null;
            if ($tujuan !== null) {
                return redirect($tujuan);
            }
            // Role tanpa dashboard: keluarkan agar tidak loop / -> /home -> /.
            auth()->logout();
        }
        return redirect('/');
    }
}
