<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function create()
    {
        return view('auth.register', [
            'daftarProgramStudi' => ProgramStudi::all(),
        ]);
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'program-studi' => 'required',
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

    public function emailVerification(User $user){
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

    public function authenticate(Request $request)
    {
        $user = User::where('username', $request->input('username'))->first();
        if (!$user) {
            return back()->withErrors(['username' => 'Username/NPM salah atau tidak terdaftar'])->withInput();
        }
        $credentials = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            if (auth()->user()->role_id == 1) {
                return redirect('/admin')->with('success', 'Anda berhasil login');
            } elseif (auth()->user()->role_id == 2) {
                return redirect('/mahasiswa')->with('success', 'Anda berhasil login');
            } elseif (auth()->user()->role_id == 3) {
                return redirect('/staff')->with('success', 'Anda berhasil login');
            } elseif (auth()->user()->role_id == 4) {
                return redirect('/kaprodi')->with('success', 'Anda berhasil login');
            } elseif (auth()->user()->role_id == 5) {
                return redirect('/wd')->with('success', 'Anda berhasil login');
            } elseif (auth()->user()->role_id == 6) {
                return redirect('/akademik')->with('success', 'Anda berhasil login');
            }
        }

        return back()->withErrors('Username atau password tidak sesuai, silahkan coba lagi')->withInput();
    }

    public function home()
    {
        if (auth()->check()) {
            if (auth()->user()->role_id == 1) {
                return redirect('/admin');
            } elseif (auth()->user()->role_id == 2) {
                return redirect('/mahasiswa');
            } elseif (auth()->user()->role_id == 3) {
                return redirect('/staff');
            } elseif (auth()->user()->role_id == 4) {
                return redirect('/kaprodi');
            } elseif (auth()->user()->role_id == 5) {
                return redirect('/wd');
            } elseif (auth()->user()->role_id == 6) {
                return redirect('/akademik');
            }
        }
        return redirect('/');
    }
}
