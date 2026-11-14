<?php

namespace App\Filament\Resources\SsoClientResource\Pages;

use App\Filament\Resources\SsoClientResource;
use App\Models\OAuthClient;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditSsoClient extends EditRecord
{
    protected static string $resource = SsoClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('regenerateSecret')
                ->label('Regenerate Secret')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate client secret?')
                ->modalDescription('Secret lama akan langsung tidak berlaku. Aplikasi klien harus diperbarui.')
                ->action(function (OAuthClient $record) {
                    $plain = Str::random(40);
                    $record->forceFill(['secret' => $plain])->save();
                    $record->tokens()->update(['revoked' => true]);

                    Notification::make()
                        ->title('Secret baru dibuat')
                        ->body("Client Secret: {$plain}\n\nSimpan sekarang — tidak akan ditampilkan lagi.")
                        ->persistent()
                        ->success()
                        ->send();
                }),

            Actions\Action::make('revokeClient')
                ->label('Revoke Client')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Revoke klien ini?')
                ->modalDescription('Seluruh token aktif akan dicabut. Klien tidak bisa dipakai sampai diaktifkan kembali.')
                ->action(function (OAuthClient $record) {
                    $record->forceFill(['is_active' => false, 'revoked' => true])->save();
                    $record->tokens()->update(['revoked' => true]);

                    Notification::make()
                        ->title('Klien dicabut')
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Saat allowlist role berubah, token user yang tidak lagi diizinkan harus
     * dicabut agar refresh token tidak tetap hidup hingga 30 hari.
     */
    protected function afterSave(): void
    {
        /** @var OAuthClient $client */
        $client = $this->record;

        $allowedRoleIds = $client->roles()->pluck('roles.id')->all();

        $client->tokens()
            ->where('revoked', false)
            ->with('user')
            ->get()
            ->each(function ($token) use ($allowedRoleIds) {
                if (! $token->user || ! in_array($token->user->role_id, $allowedRoleIds, true)) {
                    $token->forceFill(['revoked' => true])->save();
                }
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
