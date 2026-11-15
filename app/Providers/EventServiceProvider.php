<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // AuthenticateSession (panel admin) mencocokkan hash password di
        // session dengan user yang login. Laravel 10 tidak memperbaruinya
        // saat login, jadi berganti akun di browser yang sama (mis. mahasiswa
        // via SSO lalu admin) membuat hash lama tidak cocok dan admin
        // di-logout diam-diam. Segarkan hash setiap login berhasil.
        Event::listen(function (Login $event): void {
            if (request()->hasSession()) {
                request()->session()->put('password_hash_'.$event->guard, $event->user->getAuthPassword());
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
