<?php

/**
 * USPS API v3 - OAuth2 + REST/JSON
 * Replaces the legacy XML ShippingAPI.dll
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

abstract class USPSBase
{
    const API_URL = 'https://apis.usps.com';
    const TOKEN_URL = '/oauth2/v3/token';

    protected string $clientId;
    protected string $clientSecret;
    protected ?string $accessToken = null;

    protected int $errorCode = 0;
    protected string $errorMessage = '';
    protected array $response = [];

    public function __construct(string $clientId = '', string $clientSecret = '')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get an OAuth2 access token, cached until expiry.
     */
    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cacheKey = 'usps_oauth_token_' . md5($this->clientId);

        $this->accessToken = Cache::remember($cacheKey, 3000, function () {
            $response = Http::asForm()->post(self::API_URL . self::TOKEN_URL, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'addresses',
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('USPS OAuth token request failed: ' . $response->body());
            }

            return $response->json('access_token');
        });

        return $this->accessToken;
    }

    /**
     * Make an authenticated GET request to the USPS API.
     */
    protected function apiGet(string $path, array $query = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->get(self::API_URL . $path, $query);

        $this->response = $response->json() ?? [];

        if (!$response->successful()) {
            $error = $this->response['error'] ?? [];
            $this->errorCode = (int) ($error['code'] ?? $response->status());
            $this->errorMessage = $error['message'] ?? 'USPS API request failed';
            return $this->response;
        }

        $this->errorCode = 0;
        $this->errorMessage = '';

        return $this->response;
    }

    public function isError(): bool
    {
        return $this->errorCode !== 0;
    }

    public function isSuccess(): bool
    {
        return !$this->isError();
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
