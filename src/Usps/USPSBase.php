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
use Illuminate\Support\Facades\Log;
use Johnpaulmedina\Usps\Exceptions\AuthenticationException;
use Johnpaulmedina\Usps\Exceptions\ConfigurationException;

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

    /**
     * The OAuth scope(s) required by this API class.
     * Subclasses should override this to specify their scope.
     */
    protected string $scope = 'addresses';

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

        $cacheKey = 'usps_oauth_token_' . hash('sha256', $this->clientId . '_' . $this->scope);

        $this->accessToken = Cache::remember($cacheKey, 2700, function (): string {
            if (empty($this->clientId) || empty($this->clientSecret)) {
                throw new ConfigurationException('USPS API credentials not configured. Set USPS_CLIENT_ID and USPS_CLIENT_SECRET.');
            }

            $response = Http::asForm()->post(self::API_URL . self::TOKEN_URL, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => $this->scope,
            ]);

            if (!$response->successful()) {
                Log::error('USPS OAuth token request failed', [
                    'status' => $response->status(),
                    'scope' => $this->scope,
                ]);
                throw new AuthenticationException('USPS OAuth token request failed (HTTP ' . $response->status() . ').', $response->status());
            }

            return $response->json('access_token');
        });

        return $this->accessToken;
    }

    /**
     * Make an authenticated GET request to the USPS API.
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    protected function apiGet(string $path, array $query = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->get(self::API_URL . $path, $query);

        return $this->handleResponse($response);
    }

    /**
     * Make an authenticated POST request to the USPS API.
     *
     * @param array<mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function apiPost(string $path, array $data = [], array $headers = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->withHeaders($headers)
            ->post(self::API_URL . $path, $data);

        return $this->handleResponse($response);
    }

    /**
     * Make an authenticated PUT request to the USPS API.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function apiPut(string $path, array $data = [], array $headers = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->withHeaders($headers)
            ->put(self::API_URL . $path, $data);

        return $this->handleResponse($response);
    }

    /**
     * Make an authenticated PATCH request to the USPS API.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function apiPatch(string $path, array $data = [], array $headers = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->withHeaders($headers)
            ->patch(self::API_URL . $path, $data);

        return $this->handleResponse($response);
    }

    /**
     * Make an authenticated DELETE request to the USPS API.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    protected function apiDelete(string $path, array $data = [], array $headers = []): array
    {
        $response = Http::withToken($this->getAccessToken())
            ->accept('application/json')
            ->withHeaders($headers)
            ->delete(self::API_URL . $path, $data);

        return $this->handleResponse($response);
    }

    /**
     * Handle the HTTP response, setting error state and returning the body.
     *
     * @return array<string, mixed>
     */
    protected function handleResponse(\Illuminate\Http\Client\Response $response): array
    {
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

    /**
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}
