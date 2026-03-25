<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Labels;
use Orchestra\Testbench\TestCase;

class LabelsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_labels'), 'fake-token', 3600);
    }

    private function labels(): Labels
    {
        return new Labels('test-id', 'test-secret');
    }

    public function test_create_label(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/label' => Http::response(['trackingNumber' => '9205500000000000000000']),
        ]);

        $result = $this->labels()->createLabel(['toAddress' => []], 'payment-token', 'idem-key');

        $this->assertArrayHasKey('trackingNumber', $result);
        Http::assertSent(fn ($r) => $r->hasHeader('X-Payment-Authorization-Token', 'payment-token')
            && $r->hasHeader('X-Idempotency-Key', 'idem-key'));
    }

    public function test_create_return_label(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/return-label' => Http::response(['trackingNumber' => '920']),
        ]);

        $result = $this->labels()->createReturnLabel([], 'token');
        $this->assertArrayHasKey('trackingNumber', $result);
    }

    public function test_cancel_label(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/label/*' => Http::response([]),
        ]);

        $this->labels()->cancelLabel('9205500000000000000000');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_edit_label(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/label/*' => Http::response(['updated' => true]),
        ]);

        $result = $this->labels()->editLabel('9205500000000000000000', ['containers' => []]);
        $this->assertArrayHasKey('updated', $result);
        Http::assertSent(fn ($r) => $r->method() === 'PATCH');
    }

    public function test_create_indicia(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/indicia' => Http::response(['indiciaNumber' => '123']),
        ]);

        $result = $this->labels()->createIndicia([], 'token');
        $this->assertArrayHasKey('indiciaNumber', $result);
    }

    public function test_create_imb(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/indicia/imb' => Http::response(['imb' => '00000']),
        ]);

        $result = $this->labels()->createImb([], 'token');
        $this->assertArrayHasKey('imb', $result);
    }

    public function test_cancel_imb(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/indicia/imb/*' => Http::response([]),
        ]);

        $this->labels()->cancelImb('00000');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_branding_crud(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/branding*' => Http::response(['imageUUID' => 'uuid-1']),
        ]);

        $this->labels()->uploadBranding(['image' => 'data']);
        $this->labels()->listBranding(10, 0);
        $this->labels()->getBranding('uuid-1');
        $this->labels()->deleteBranding('uuid-1');
        $this->labels()->renameBranding('uuid-1', ['name' => 'new']);

        Http::assertSentCount(5);
    }

    public function test_reprint_label(): void
    {
        Http::fake([
            'apis.usps.com/labels/v3/label-reprint/*' => Http::response(['trackingNumber' => '920']),
        ]);

        $result = $this->labels()->reprintLabel('920', [], 'token');
        $this->assertArrayHasKey('trackingNumber', $result);
    }
}
