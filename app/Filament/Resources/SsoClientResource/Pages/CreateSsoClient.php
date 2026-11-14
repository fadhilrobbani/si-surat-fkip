<?php

namespace App\Filament\Resources\SsoClientResource\Pages;

use App\Filament\Resources\SsoClientResource;
use App\Models\OAuthClient;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSsoClient extends CreateRecord
{
    protected static string $resource = SsoClientResource::class;

    /** Plain secret disimpan sementara agar bisa ditampilkan sekali setelah create. */
    protected ?string $plainSecret = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainSecret = Str::random(40);

        $data['secret'] = $this->plainSecret;
        $data['grant_types'] = ['authorization_code', 'refresh_token'];
        $data['personal_access_client'] = false;
        $data['password_client'] = false;
        $data['revoked'] = false;
        $data['user_id'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var OAuthClient $client */
        $client = $this->record;

        Notification::make()
            ->title('Klien SSO berhasil dibuat')
            ->body(
                "Client ID: {$client->getKey()}\n\n".
                "Client Secret: {$this->plainSecret}\n\n".
                'Simpan secret ini sekarang — tidak akan ditampilkan lagi.'
            )
            ->persistent()
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
