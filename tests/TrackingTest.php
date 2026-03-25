<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Tracking;
use Orchestra\Testbench\TestCase;

class TrackingTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_tracking'), 'fake-token', 3600);
    }

    private function tracking(): Tracking
    {
        return new Tracking('test-id', 'test-secret');
    }

    public function test_track_sends_post_request(): void
    {
        Http::fake([
            'apis.usps.com/tracking/v3r2/tracking' => Http::response([
                ['trackingNumber' => '9400111899223456789012', 'status' => 'Delivered'],
            ]),
        ]);

        $result = $this->tracking()->track([
            ['trackingNumber' => '9400111899223456789012'],
        ]);

        $this->assertIsArray($result);
        Http::assertSent(fn ($r) => $r->method() === 'POST' && str_contains($r->url(), '/tracking/v3r2/tracking'));
    }

    public function test_register_notifications(): void
    {
        Http::fake([
            'apis.usps.com/tracking/v3r2/tracking/*/notifications' => Http::response([
                'transactionMessage' => 'Notification registered.',
            ], 202),
        ]);

        $result = $this->tracking()->registerNotifications('123456', [
            'uniqueTrackingID' => 'abc',
            'notifyEventTypes' => ['ALL_UPDATES'],
            'recipients' => [['email' => 'test@example.com']],
        ]);

        $this->assertArrayHasKey('transactionMessage', $result);
    }

    public function test_proof_of_delivery(): void
    {
        Http::fake([
            'apis.usps.com/tracking/v3r2/tracking/*/proof-of-delivery' => Http::response([
                'transactionMessage' => 'Request accepted.',
            ], 202),
        ]);

        $result = $this->tracking()->proofOfDelivery('123456', [
            'uniqueTrackingID' => 'abc',
            'recipients' => [['email' => 'test@example.com', 'firstName' => 'John', 'lastName' => 'Doe']],
        ]);

        $this->assertArrayHasKey('transactionMessage', $result);
    }
}
