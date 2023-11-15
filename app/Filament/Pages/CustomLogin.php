<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;


class CustomLogin extends Login
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if (app()->environment('local')) {
            $this->form->fill([
                'username' => 'admin',
                'password' => 'password',
            ]);
        }
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);

            // dd($user->role->id);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        // dd($data['username']);
        $user = User::where('username',$data['username'])->where('role_id',1)->first();
        if($user == null){
            throw ValidationException::withMessages([
                'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
        // dd($user);
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('Admin Login');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Admin Login');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1])
            ->autocomplete();
    }
}
