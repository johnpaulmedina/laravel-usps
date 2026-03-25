<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\ScanForm;
use Orchestra\Testbench\TestCase;

class ScanFormTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_scan-forms'), 'fake-token', 3600);
    }

    private function client(): ScanForm
    {
        return new ScanForm('test-id', 'test-secret');
    }

    public function test_create_label_shipment(): void
    {
        Http::fake([
            'apis.usps.com/scan-forms/v3/scan-form' => Http::response([
                'electronicFileNumber' => 'EFN123456',
                'status' => 'CREATED',
                'acceptedCount' => 3,
                'rejectedCount' => 0,
            ]),
        ]);

        $result = $this->client()->createLabelShipment([
            'trackingNumbers' => ['9400111899223456789012', '9400111899223456789013', '9400111899223456789014'],
        ]);

        $this->assertEquals('EFN123456', $result['electronicFileNumber']);
        $this->assertEquals(3, $result['acceptedCount']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_contains($request->url(), 'scan-forms/v3/scan-form')
                && $body['scanFormType'] === 'LABEL_SHIPMENT'
                && count($body['trackingNumbers']) === 3;
        });
    }

    public function test_create_mid_shipment(): void
    {
        Http::fake([
            'apis.usps.com/scan-forms/v3/scan-form' => Http::response([
                'electronicFileNumber' => 'EFN789012',
                'status' => 'CREATED',
            ]),
        ]);

        $result = $this->client()->createMidShipment([
            'MID' => '123456',
            'trackingNumbers' => ['9400111899223456789012'],
        ]);

        $this->assertEquals('EFN789012', $result['electronicFileNumber']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['scanFormType'] === 'MID_SHIPMENT'
                && $body['MID'] === '123456';
        });
    }

    public function test_create_manifest_mid_shipment(): void
    {
        Http::fake([
            'apis.usps.com/scan-forms/v3/scan-form' => Http::response([
                'electronicFileNumber' => 'EFN345678',
                'status' => 'CREATED',
            ]),
        ]);

        $result = $this->client()->createManifestMidShipment([
            'manifestMID' => '654321',
            'MID' => '123456',
        ]);

        $this->assertEquals('EFN345678', $result['electronicFileNumber']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['scanFormType'] === 'MANIFEST_MID_SHIPMENT'
                && $body['manifestMID'] === '654321';
        });
    }

    public function test_scan_form_with_override_address(): void
    {
        Http::fake([
            'apis.usps.com/scan-forms/v3/scan-form' => Http::response([
                'electronicFileNumber' => 'EFN999',
                'status' => 'CREATED',
            ]),
        ]);

        $this->client()->createLabelShipment([
            'trackingNumbers' => ['9400111899223456789012'],
            'overrideAddress' => [
                'streetAddress' => '123 Main St',
                'city' => 'Miami',
                'state' => 'FL',
                'ZIPCode' => '33101',
            ],
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['overrideAddress'])
                && $body['overrideAddress']['city'] === 'Miami';
        });
    }

    public function test_scan_form_handles_error(): void
    {
        Http::fake([
            'apis.usps.com/scan-forms/v3/scan-form' => Http::response([
                'error' => ['code' => '400', 'message' => 'Invalid tracking number.'],
            ], 400),
        ]);

        $client = $this->client();
        $client->createLabelShipment([
            'trackingNumbers' => ['INVALID'],
        ]);

        $this->assertTrue($client->isError());
        $this->assertEquals('Invalid tracking number.', $client->getErrorMessage());
    }
}
