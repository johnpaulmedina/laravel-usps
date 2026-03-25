<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\InternationalLabels;
use Orchestra\Testbench\TestCase;

class InternationalLabelsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_international-labels'), 'fake-token', 3600);
    }

    private function api(): InternationalLabels
    {
        return new InternationalLabels('test-id', 'test-secret');
    }

    public function test_create_international_label(): void
    {
        Http::fake([
            'apis.usps.com/international-labels/v3/international-label' => Http::response(['trackingNumber' => 'CX123456789US']),
        ]);

        $result = $this->api()->createLabel(['toAddress' => []], 'payment-token');
        $this->assertArrayHasKey('trackingNumber', $result);
    }

    public function test_reprint_international_label(): void
    {
        Http::fake([
            'apis.usps.com/international-labels/v3/international-label-reprint/*' => Http::response(['trackingNumber' => 'CX123456789US']),
        ]);

        $result = $this->api()->reprintLabel('CX123456789US', [], 'token');
        $this->assertArrayHasKey('trackingNumber', $result);
    }

    public function test_cancel_international_label(): void
    {
        Http::fake([
            'apis.usps.com/international-labels/v3/international-label/*' => Http::response([]),
        ]);

        $this->api()->cancelLabel('CX123456789US');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_create_international_indicia(): void
    {
        Http::fake([
            'apis.usps.com/international-labels/v3/indicia' => Http::response(['indiciaNumber' => '456']),
        ]);

        $result = $this->api()->createIndicia([], 'token');
        $this->assertArrayHasKey('indiciaNumber', $result);
    }
}
