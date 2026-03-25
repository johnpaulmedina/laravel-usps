<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class CommandsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('usps.client_id', 'test-id');
        $app['config']->set('usps.client_secret', 'test-secret');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_addresses'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_tracking'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_prices'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_service-standards'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_locations'), 'fake-token', 3600);
    }

    public function test_validate_address_command(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '1600 PENNSYLVANIA AVE NW',
                    'city' => 'WASHINGTON',
                    'state' => 'DC',
                    'ZIPCode' => '20500',
                    'ZIPPlus4' => '0005',
                ],
            ]),
        ]);

        $this->artisan('usps:validate', [
            'street' => '1600 Pennsylvania Ave NW',
            '--state' => 'DC',
            '--zip' => '20500',
        ])->assertSuccessful();
    }

    public function test_track_package_command(): void
    {
        Http::fake([
            'apis.usps.com/tracking/v3r2/tracking' => Http::response([
                'trackingResults' => [[
                    'trackingNumber' => '9400111899223456789012',
                    'statusCategory' => 'Delivered',
                    'trackingEvents' => [[
                        'eventDate' => '2026-03-24',
                        'eventTime' => '10:30',
                        'event' => 'Delivered',
                        'eventCity' => 'MIAMI',
                        'eventState' => 'FL',
                        'eventZIPCode' => '33101',
                    ]],
                ]],
            ]),
        ]);

        $this->artisan('usps:track', [
            'tracking' => ['9400111899223456789012'],
        ])->assertSuccessful();
    }

    public function test_zip_lookup_command(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/city-state*' => Http::response([
                'city' => 'MIAMI',
                'state' => 'FL',
                'ZIPCode' => '33101',
            ]),
        ]);

        $this->artisan('usps:zip', ['zip' => '33101'])
            ->assertSuccessful();
    }

    public function test_price_calculator_command(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/base-rates/search' => Http::response([
                'rates' => [[
                    'mailClass' => 'USPS_GROUND_ADVANTAGE',
                    'totalBasePrice' => 5.25,
                    'deliveryDays' => '3-5',
                ]],
            ]),
        ]);

        $this->artisan('usps:price', [
            'origin' => '20500',
            'destination' => '33101',
            'weight' => '16',
        ])->assertSuccessful();
    }

    public function test_service_standards_command(): void
    {
        Http::fake([
            'apis.usps.com/service-standards/v3/estimates*' => Http::response([
                'estimates' => [[
                    'mailClass' => 'PRIORITY_MAIL',
                    'deliveryDate' => '2026-03-27',
                    'deliveryDays' => '2',
                    'acceptanceDate' => '2026-03-25',
                ]],
            ]),
        ]);

        $this->artisan('usps:standards', [
            'origin' => '20500',
            'destination' => '33101',
        ])->assertSuccessful();
    }

    public function test_find_locations_command(): void
    {
        Http::fake([
            'apis.usps.com/locations/v3/post-office-locations*' => Http::response([
                'postOffices' => [[
                    'facilityName' => 'MIAMI POST OFFICE',
                    'streetAddress' => '500 NW 2ND AVE',
                    'city' => 'MIAMI',
                    'state' => 'FL',
                    'ZIPCode' => '33101',
                    'distance' => '0.5',
                ]],
            ]),
        ]);

        $this->artisan('usps:locations', ['zip' => '33101'])
            ->assertSuccessful();
    }

    public function test_validate_command_handles_error(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'error' => ['code' => '404', 'message' => 'Address Not Found.'],
            ], 404),
        ]);

        $this->artisan('usps:validate', [
            'street' => '99999 Fake St',
            '--state' => 'XX',
        ])->assertFailed();
    }
}
