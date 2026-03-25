<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Usps;
use Orchestra\Testbench\TestCase;

class UspsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_addresses'), 'fake-token', 3600);
    }

    private function usps(): Usps
    {
        return new Usps(['client_id' => 'test-id', 'client_secret' => 'test-secret']);
    }

    public function test_validate_returns_address_on_success(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '14571 SW 20TH ST',
                    'city' => 'MIAMI',
                    'state' => 'FL',
                    'ZIPCode' => '33175',
                    'ZIPPlus4' => '1234',
                ],
            ]),
        ]);

        $result = $this->usps()->validate([
            'Address' => '14571 SW 20TH ST',
            'City' => 'Miami',
            'State' => 'FL',
            'Zip' => '33175',
        ]);

        $this->assertArrayHasKey('address', $result);
        $this->assertEquals('14571 SW 20TH ST', $result['address']['Address2']);
        $this->assertEquals('MIAMI', $result['address']['City']);
        $this->assertEquals('FL', $result['address']['State']);
    }

    public function test_validate_returns_error_on_failure(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'error' => ['code' => '404', 'message' => 'Address Not Found.'],
            ], 404),
        ]);

        $result = $this->usps()->validate([
            'Address' => '99999 Fake St',
            'State' => 'XX',
            'Zip' => '00000',
        ]);

        $this->assertArrayHasKey('error', $result);
    }

    public function test_validate_handles_case_insensitive_input(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '123 MAIN ST',
                    'city' => 'MIAMI',
                    'state' => 'FL',
                    'ZIPCode' => '33101',
                    'ZIPPlus4' => null,
                ],
            ]),
        ]);

        $result = $this->usps()->validate([
            'address' => '123 Main St',
            'city' => 'Miami',
            'state' => 'FL',
            'zip' => '33101',
        ]);

        $this->assertArrayHasKey('address', $result);
    }

    public function test_city_state_lookup(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/city-state*' => Http::response([
                'city' => 'MIAMI',
                'state' => 'FL',
                'ZIPCode' => '33101',
            ]),
        ]);

        $result = $this->usps()->cityStateLookup('33101');

        $this->assertEquals('MIAMI', $result['city']);
        $this->assertEquals('FL', $result['state']);
    }

    public function test_zip_code_lookup(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/zipcode*' => Http::response([
                'address' => [
                    'streetAddress' => '1600 PENNSYLVANIA AVE NW',
                    'city' => 'WASHINGTON',
                    'state' => 'DC',
                    'ZIPCode' => '20500',
                    'ZIPPlus4' => '0005',
                ],
            ]),
        ]);

        $result = $this->usps()->zipCodeLookup([
            'streetAddress' => '1600 Pennsylvania Ave NW',
            'city' => 'Washington',
            'state' => 'DC',
        ]);

        $this->assertEquals('20500', $result['address']['ZIPCode']);
    }
}
