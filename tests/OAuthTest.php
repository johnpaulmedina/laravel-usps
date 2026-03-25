<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Address;
use Johnpaulmedina\Usps\AddressVerify;
use Orchestra\Testbench\TestCase;

class OAuthTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    public function test_oauth_token_is_fetched_and_cached(): void
    {
        Cache::flush();

        Http::fake([
            'apis.usps.com/oauth2/v3/token' => Http::response([
                'access_token' => 'new-oauth-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ]),
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '123 MAIN ST',
                    'city' => 'MIAMI',
                    'state' => 'FL',
                    'ZIPCode' => '33101',
                ],
            ]),
        ]);

        $verify = new AddressVerify('my-client-id', 'my-client-secret');

        $address = new Address;
        $address->setAddress('123 Main St')->setState('FL')->setZip5('33101');
        $verify->addAddress($address);
        $verify->verify();

        // Token should be cached (key includes scope)
        $cacheKey = 'usps_oauth_token_' . hash('sha256', 'my-client-id_addresses');
        $this->assertEquals('new-oauth-token', Cache::get($cacheKey));

        // OAuth token request was sent with correct params
        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), 'oauth2/v3/token')) {
                return false;
            }
            return $request->data()['grant_type'] === 'client_credentials'
                && $request->data()['client_id'] === 'my-client-id'
                && $request->data()['client_secret'] === 'my-client-secret'
                && $request->data()['scope'] === 'addresses';
        });

        // Address request was sent with Bearer token
        Http::assertSent(function ($request) {
            if (!str_contains($request->url(), 'addresses/v3/address')) {
                return false;
            }
            return $request->hasHeader('Authorization', 'Bearer new-oauth-token');
        });
    }

    public function test_oauth_token_failure_throws(): void
    {
        Cache::flush();

        Http::fake([
            'apis.usps.com/oauth2/v3/token' => Http::response(['error' => 'invalid_client'], 401),
        ]);

        $verify = new AddressVerify('bad-id', 'bad-secret');

        $address = new Address;
        $address->setAddress('123 Main St')->setState('FL')->setZip5('33101');
        $verify->addAddress($address);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('USPS OAuth token request failed');

        $verify->verify();
    }

    public function test_cached_token_is_reused(): void
    {
        $cacheKey = 'usps_oauth_token_' . hash('sha256', 'cached-client_addresses');
        Cache::put($cacheKey, 'cached-token', 3600);

        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '123 MAIN ST',
                    'city' => 'MIAMI',
                    'state' => 'FL',
                    'ZIPCode' => '33101',
                ],
            ]),
        ]);

        $verify = new AddressVerify('cached-client', 'secret');

        $address = new Address;
        $address->setAddress('123 Main St')->setState('FL')->setZip5('33101');
        $verify->addAddress($address);
        $verify->verify();

        // No token request should have been made
        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'oauth2/v3/token');
        });

        // Address request used the cached token
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'addresses/v3/address')
                && $request->hasHeader('Authorization', 'Bearer cached-token');
        });
    }
}
