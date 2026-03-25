<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\CarrierPickup;
use Orchestra\Testbench\TestCase;

class CarrierPickupTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_carrier-pickup'), 'fake-token', 3600);
    }

    private function api(): CarrierPickup
    {
        return new CarrierPickup('test-id', 'test-secret');
    }

    public function test_check_eligibility(): void
    {
        Http::fake([
            'apis.usps.com/pickup/v3/carrier-pickup/eligibility*' => Http::response([
                'address' => ['streetAddress' => '123 MAIN ST', 'city' => 'MIAMI', 'state' => 'FL'],
            ]),
        ]);

        $result = $this->api()->checkEligibility('123 Main St', ['ZIPCode' => '33101']);
        $this->assertArrayHasKey('address', $result);
    }

    public function test_schedule_pickup(): void
    {
        Http::fake([
            'apis.usps.com/pickup/v3/carrier-pickup' => Http::response([
                'confirmationNumber' => 'WTC12345',
            ]),
        ]);

        $result = $this->api()->schedulePickup([
            'pickupDate' => '2026-04-01',
            'pickupAddress' => ['firstName' => 'John', 'lastName' => 'Doe', 'address' => ['streetAddress' => '123 Main']],
            'packages' => [['packageType' => 'PRIORITY_MAIL', 'packageCount' => 2]],
            'estimatedWeight' => 5.0,
            'pickupLocation' => ['packageLocation' => 'FRONT_DOOR'],
        ]);

        $this->assertEquals('WTC12345', $result['confirmationNumber']);
        Http::assertSent(fn ($r) => $r->method() === 'POST');
    }

    public function test_get_pickup(): void
    {
        Http::fake([
            'apis.usps.com/pickup/v3/carrier-pickup/*' => Http::response([
                ['confirmationNumber' => 'WTC12345', 'pickupDate' => '2026-04-01'],
            ]),
        ]);

        $result = $this->api()->getPickup('WTC12345');
        $this->assertIsArray($result);
    }

    public function test_update_pickup(): void
    {
        Http::fake([
            'apis.usps.com/pickup/v3/carrier-pickup/*' => Http::response([
                'confirmationNumber' => 'WTC12345',
            ]),
        ]);

        $this->api()->updatePickup('WTC12345', ['pickupDate' => '2026-04-02'], 'etag-value');
        Http::assertSent(fn ($r) => $r->method() === 'PUT' && $r->hasHeader('If-Match', 'etag-value'));
    }

    public function test_cancel_pickup(): void
    {
        Http::fake([
            'apis.usps.com/pickup/v3/carrier-pickup/*' => Http::response([], 200),
        ]);

        $this->api()->cancelPickup('WTC12345', 'etag-value');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE' && $r->hasHeader('If-Match', 'etag-value'));
    }
}
