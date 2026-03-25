<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Campaigns;
use Johnpaulmedina\Usps\PackageCampaigns;
use Orchestra\Testbench\TestCase;

class CampaignsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_informed-delivery-campaigns'), 'fake-token', 3600);
        Cache::put('usps_oauth_token_' . md5('test-id_informed-delivery-package-campaigns'), 'fake-token', 3600);
    }

    public function test_create_mail_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns' => Http::response(['campaignId' => 'CAM-001']),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->createCampaign(['campaignTitle' => 'Test Campaign']);
        $this->assertArrayHasKey('campaignId', $result);
    }

    public function test_search_mail_campaigns(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns*' => Http::response([
                'campaigns' => [['campaignId' => 'CAM-001']],
            ]),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->searchCampaigns(['status' => 'ACTIVE']);
        $this->assertArrayHasKey('campaigns', $result);
    }

    public function test_get_single_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*' => Http::response(['campaignId' => 'CAM-001']),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->getCampaign('CAM-001');
        $this->assertEquals('CAM-001', $result['campaignId']);
    }

    public function test_cancel_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*' => Http::response([]),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $api->cancelCampaign('CAM-001');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_update_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*' => Http::response(['campaignId' => 'CAM-001']),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $api->updateCampaign('CAM-001', ['campaignTitle' => 'Updated']);
        Http::assertSent(fn ($r) => $r->method() === 'PUT');
    }

    public function test_add_imbs(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*/imbs' => Http::response(['status' => 'accepted']),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->addImbs('CAM-001', ['imbs' => ['00000000000000000000']]);
        $this->assertArrayHasKey('status', $result);
    }

    public function test_get_callback_keys(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*/callbacks*' => Http::response(['callbacks' => []]),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->getCallbackKeys('12345');
        $this->assertArrayHasKey('callbacks', $result);
    }

    public function test_get_callback_summary(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*/*/summary' => Http::response(['totalCount' => 10]),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->getCallbackSummary('12345', 'key-abc');
        $this->assertArrayHasKey('totalCount', $result);
    }

    public function test_get_callback_details(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-campaigns/v3/campaigns/*/*/details' => Http::response(['errors' => []]),
        ]);

        $api = new Campaigns('test-id', 'test-secret');
        $result = $api->getCallbackDetails('12345', 'key-abc');
        $this->assertArrayHasKey('errors', $result);
    }

    // --- Package Campaigns ---

    public function test_create_package_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns' => Http::response(['campaignId' => 'PKG-001']),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $result = $api->createCampaign(['campaignTitle' => 'Package Campaign']);
        $this->assertArrayHasKey('campaignId', $result);
    }

    public function test_search_package_campaigns(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns*' => Http::response(['campaigns' => []]),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $result = $api->searchCampaigns();
        $this->assertArrayHasKey('campaigns', $result);
    }

    public function test_get_single_package_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns/*' => Http::response(['campaignId' => 'PKG-001']),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $result = $api->getCampaign('PKG-001');
        $this->assertEquals('PKG-001', $result['campaignId']);
    }

    public function test_update_package_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns/*' => Http::response([]),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $api->updateCampaign('PKG-001', ['campaignTitle' => 'Updated']);
        Http::assertSent(fn ($r) => $r->method() === 'PUT');
    }

    public function test_cancel_package_campaign(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns/*' => Http::response([]),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $api->cancelCampaign('PKG-001');
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_add_tracking_numbers(): void
    {
        Http::fake([
            'apis.usps.com/informed-delivery-package-campaigns/v3/package-campaigns/*/tracking-numbers' => Http::response(['status' => 'accepted']),
        ]);

        $api = new PackageCampaigns('test-id', 'test-secret');
        $result = $api->addTrackingNumbers('PKG-001', ['trackingNumbers' => ['9400111899223456789012']]);
        $this->assertArrayHasKey('status', $result);
    }
}
