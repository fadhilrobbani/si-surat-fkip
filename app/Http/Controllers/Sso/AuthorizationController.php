<?php

namespace App\Http\Controllers\Sso;

use App\Models\OAuthClient;
use App\Services\Sso\ClientAccessPolicy;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\Http\Controllers\AuthorizationController as PassportAuthorizationController;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Authorization endpoint dengan gerbang izin SSO.
 *
 * Passport route didaftarkan oleh package service provider yang boot lebih awal
 * dari RouteServiceProvider, sehingga override lewat routes/web.php akan kalah
 * prioritas. Binding container (lihat AppServiceProvider::register) adalah cara
 * yang benar untuk mengganti implementasi ini.
 */
class AuthorizationController extends PassportAuthorizationController
{
    public function __construct(
        AuthorizationServer $server,
        StatefulGuard $guard,
        AuthorizationViewResponse $response,
        protected ClientAccessPolicy $accessPolicy,
    ) {
        parent::__construct($server, $guard, $response);
    }

    public function authorize(
        ServerRequestInterface $psrRequest,
        Request $request,
        ClientRepository $clients,
        TokenRepository $tokens
    ) {
        $client = $this->resolveClient($request);

        if (! $client) {
            return $this->denied($request, null, 'inactive');
        }

        // User belum login: biarkan Passport menangani (redirect ke halaman login).
        if ($this->guard->guest()) {
            return parent::authorize($psrRequest, $request, $clients, $tokens);
        }

        $user = $this->guard->user();

        if (! $this->accessPolicy->isAllowed($user, $client)) {
            return $this->denied($request, $client, $this->accessPolicy->denialReason($user, $client));
        }

        return parent::authorize($psrRequest, $request, $clients, $tokens);
    }

    protected function resolveClient(Request $request): ?OAuthClient
    {
        $clientId = $request->query('client_id');

        if (! $clientId) {
            return null;
        }

        return OAuthClient::query()->find($clientId);
    }

    protected function denied(Request $request, ?OAuthClient $client, string $reason)
    {
        return response()->view('oauth.denied', [
            'client' => $client,
            'reason' => $reason,
            'user' => $this->guard->user(),
        ], 403);
    }
}
