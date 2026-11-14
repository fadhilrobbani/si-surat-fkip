<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        // Livewire::setUpdateRoute(function ($handle) {
        //     $url =  'appname/public/livewire/update';
        //     return Route::post($url, $handle);
        // });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            $this->registerOAuthRoutes();
        });
    }

    /**
     * Route OAuth2 yang dibutuhkan SSO saja.
     *
     * Passport::ignoreRoutes() dipanggil di AuthServiceProvider karena route
     * bawaan paket menyertakan manajemen klien (/oauth/clients) untuk semua
     * user login — celah yang tidak diperlukan di sini. Hanya endpoint berikut
     * yang dibuka.
     */
    protected function registerOAuthRoutes(): void
    {
        Route::group([
            'prefix' => config('passport.path', 'oauth'),
            'namespace' => 'Laravel\Passport\Http\Controllers',
        ], function () {
            // Authorization Code Grant (+ PKCE)
            Route::post('/token', [
                'uses' => 'AccessTokenController@issueToken',
                'as' => 'passport.token',
                'middleware' => 'throttle',
            ]);

            Route::get('/authorize', [
                'uses' => 'AuthorizationController@authorize',
                'as' => 'passport.authorizations.authorize',
                'middleware' => 'web',
            ]);

            Route::middleware(['web'])->group(function () {
                Route::post('/token/refresh', [
                    'uses' => 'TransientTokenController@refresh',
                    'as' => 'passport.token.refresh',
                ]);

                Route::post('/authorize', [
                    'uses' => 'ApproveAuthorizationController@approve',
                    'as' => 'passport.authorizations.approve',
                ]);

                Route::delete('/authorize', [
                    'uses' => 'DenyAuthorizationController@deny',
                    'as' => 'passport.authorizations.deny',
                ]);
            });
        });
    }
}
