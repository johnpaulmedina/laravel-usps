<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Containers;
use Orchestra\Testbench\TestCase;

class ContainersTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_containers'), 'fake-token', 3600);
    }

    private function api(): Containers
    {
        return new Containers('test-id', 'test-secret');
    }

    public function test_create_container(): void
    {
        Http::fake([
            'apis.usps.com/containers/v3/containers' => Http::response(['containerID' => 'CNT-001'], 201),
        ]);

        $result = $this->api()->createContainer([
            'MID' => '123456',
            'mailClass' => 'PARCEL_SELECT',
            'destinationZIPCode' => '33101',
            'destinationEntryFacilityType' => 'DESTINATION_DELIVERY_UNIT',
            'sortType' => 'PALLET',
            'processingCategory' => 'MACHINABLE',
            'mailerName' => 'Test Mailer',
            'originAddress' => ['streetAddress' => '123 Main', 'city' => 'Miami', 'state' => 'FL', 'ZIPCode' => '33101'],
        ]);

        $this->assertEquals('CNT-001', $result['containerID']);
    }

    public function test_add_packages(): void
    {
        Http::fake([
            'apis.usps.com/containers/v3/containers/*/packages' => Http::response(['containerID' => 'CNT-001']),
        ]);

        $result = $this->api()->addPackages('CNT-001', ['trackingNumbers' => ['9400111899223456789012']]);
        $this->assertArrayHasKey('containerID', $result);
    }

    public function test_remove_all_packages(): void
    {
        Http::fake([
            'apis.usps.com/containers/v3/containers/*/packages' => Http::response([], 204),
        ]);

        $this->api()->removeAllPackages('CNT-001');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_remove_single_package(): void
    {
        Http::fake([
            'apis.usps.com/containers/v3/containers/*/packages/*' => Http::response([], 204),
        ]);

        $this->api()->removePackage('CNT-001', '9400111899223456789012');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_create_manifest(): void
    {
        Http::fake([
            'apis.usps.com/containers/v3/containers/manifest' => Http::response([], 204),
        ]);

        $this->api()->createManifest(['containers' => ['CNT-001']]);
        Http::assertSent(fn ($r) => $r->method() === 'POST');
    }
}
