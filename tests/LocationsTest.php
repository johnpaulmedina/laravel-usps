<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Locations;
use Orchestra\Testbench\TestCase;

class LocationsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_locations'), 'fake-token', 3600);
    }

    private function api(): Locations
    {
        return new Locations('test-id', 'test-secret');
    }

    public function test_get_dropoff_locations(): void
    {
        Http::fake([
            'apis.usps.com/locations/v3/dropoff-locations*' => Http::response([
                'locations' => [['facilityName' => 'Test Facility']],
            ]),
        ]);

        $result = $this->api()->getDropoffLocations('33101');
        $this->assertArrayHasKey('locations', $result);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'destinationZIPCode=33101'));
    }

    public function test_get_post_office_locations(): void
    {
        Http::fake([
            'apis.usps.com/locations/v3/post-office-locations*' => Http::response([
                'postOffices' => [['name' => 'Downtown PO']],
            ]),
        ]);

        $result = $this->api()->getPostOfficeLocations(['ZIPCode' => '33101', 'radius' => 10]);
        $this->assertArrayHasKey('postOffices', $result);
    }

    public function test_get_parcel_locker_locations(): void
    {
        Http::fake([
            'apis.usps.com/locations/v3/parcel-locker-locations*' => Http::response([
                'lockers' => [],
            ]),
        ]);

        $result = $this->api()->getParcelLockerLocations(['ZIPCode' => '33101']);
        $this->assertArrayHasKey('lockers', $result);
    }
}
