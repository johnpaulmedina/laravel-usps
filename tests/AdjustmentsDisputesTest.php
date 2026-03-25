<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Adjustments;
use Johnpaulmedina\Usps\Disputes;
use Orchestra\Testbench\TestCase;

class AdjustmentsDisputesTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_adjustments'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_disputes'), 'fake-token', 3600);
    }

    public function test_get_adjustments(): void
    {
        Http::fake([
            'apis.usps.com/adjustments/v3/adjustments/*/*/*' => Http::response([
                ['trackingNumber' => '920011234561234567890', 'adjustmentSubtype' => 'CENSUS', 'postageAdjustment' => 4.78],
            ]),
        ]);

        $api = new Adjustments('test-id', 'test-secret');
        $result = $api->getAdjustments('12345678', '920011234561234567890', 'CENSUS');

        $this->assertIsArray($result);
        Http::assertSent(fn ($r) => str_contains($r->url(), '/adjustments/v3/adjustments/12345678/920011234561234567890/CENSUS'));
    }

    public function test_get_adjustments_with_destination_zip(): void
    {
        Http::fake([
            'apis.usps.com/adjustments/v3/adjustments/*/*/**' => Http::response([]),
        ]);

        $api = new Adjustments('test-id', 'test-secret');
        $api->getAdjustments('12345678', '920011234561234567890', 'DUPLICATES', ['destinationZIPCode' => '33101']);

        Http::assertSent(fn ($r) => str_contains($r->url(), 'destinationZIPCode=33101'));
    }

    public function test_create_dispute(): void
    {
        Http::fake([
            'apis.usps.com/disputes/v3/dispute' => Http::response([
                'disputeID' => 'DSP-001',
                'status' => 'NEW',
                'trackingID' => '920011234561234567890',
            ]),
        ]);

        $api = new Disputes('test-id', 'test-secret');
        $result = $api->createDispute([
            'EPSTransactionID' => 'TXN-001',
            'trackingID' => '920011234561234567890',
            'CRID' => '12345678',
            'reason' => 'INCORRECT_ASSESSED_WEIGHT',
            'description' => 'Weight was measured incorrectly.',
            'name' => 'John Doe',
            'disputeCount' => '1',
        ]);

        $this->assertEquals('DSP-001', $result['disputeID']);
        $this->assertEquals('NEW', $result['status']);
    }
}
