<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\CityStateLookup;
use Orchestra\Testbench\TestCase;

class CityStateLookupTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_addresses'), 'fake-token', 3600);
    }

    private function api(): CityStateLookup
    {
        return new CityStateLookup('test-id', 'test-secret');
    }

    public function test_lookup_with_valid_zip(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/city-state*' => Http::response([
                'city' => 'MIAMI',
                'state' => 'FL',
                'ZIPCode' => '33101',
            ]),
        ]);

        $result = $this->api()->lookup('33101');

        $this->assertEquals('MIAMI', $result['city']);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'ZIPCode=33101'));
    }

    public function test_lookup_normalizes_zip_with_dash(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/city-state*' => Http::response([
                'city' => 'WASHINGTON',
                'state' => 'DC',
            ]),
        ]);

        // '20500' is valid 5-digit, dashes are stripped
        $result = $this->api()->lookup('20500');
        $this->assertIsArray($result);
    }

    public function test_lookup_rejects_invalid_zip(): void
    {
        Http::fake();

        $api = $this->api();
        $result = $api->lookup('abc');

        $this->assertTrue($api->isError());
        $this->assertEquals('Invalid ZIP code format.', $api->getErrorMessage());
        $this->assertEmpty($result);
        Http::assertNothingSent();
    }

    public function test_lookup_rejects_too_short_zip(): void
    {
        Http::fake();

        $api = $this->api();
        $result = $api->lookup('123');

        $this->assertTrue($api->isError());
        $this->assertEmpty($result);
        Http::assertNothingSent();
    }
}
