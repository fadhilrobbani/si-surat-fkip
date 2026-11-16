<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Halaman login yang terbuka melewati umur sesi menghasilkan 419;
        // kembalikan ke form login dengan pesan, bukan halaman error.
        // (TokenMismatchException sudah diubah Laravel menjadi HttpException 419.)
        $this->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419 && $request->routeIs('authLogin')) {
                return redirect()->route('login')
                    ->withInput($request->only('username'))
                    ->withErrors('Sesi halaman kedaluwarsa, silakan coba login lagi.');
            }
        });
    }

    /**
     * Guest yang diarahkan login oleh /oauth/authorize diberi penanda `sso`
     * di URL login. Hanya penanda ini yang memunculkan halaman login SSO;
     * `url.intended` saja tidak cukup karena bisa tertinggal di session.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $clientId = $request->is('oauth/authorize') ? $request->query('client_id') : null;

        if ($request->expectsJson() || ! is_string($clientId) || $clientId === '') {
            return parent::unauthenticated($request, $exception);
        }

        return redirect()->guest(route('login', ['sso' => $clientId]));
    }
}
