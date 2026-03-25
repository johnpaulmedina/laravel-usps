<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\ShippingOptions;
use Orchestra\Testbench\TestCase;

class ShippingOptionsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_shipments'), 'fake-token', 3600);
    }

    private function client(): ShippingOptions
    {
        return new ShippingOptions('test-id', 'test-secret');
    }

    public function test_search_sends_correct_payload(): void
    {
        Http::fake([
            'apis.usps.com/shipments/v3/options/search' => Http::response([
                'options' => [
                    [
                        'mailClass' => 'PRIORITY_MAIL',
                        'totalBasePrice' => 8.50,
                        'deliveryDate' => '2026-04-02',
                    ],
                    [
                        'mailClass' => 'USPS_GROUND_ADVANTAGE',
                        'totalBasePrice' => 5.25,
                        'deliveryDate' => '2026-04-05',
                    ],
                ],
            ]),
        ]);

        $result = $this->client()->search([
            'originZIPCode' => '20500',
            'destinationZIPCode' => '33101',
            'weight' => 2.5,
            'length' => 12,
            'width' => 8,
            'height' => 4,
            'mailClass' => 'PRIORITY_MAIL',
        ]);

        $this->assertArrayHasKey('options', $result);
        $this->assertCount(2, $result['options']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_contains($request->url(), 'shipments/v3/options/search')
                && $body['originZIPCode'] === '20500'
                && $body['destinationZIPCode'] === '33101'
                && $body['weight'] === 2.5
                && $body['mailClass'] === 'PRIORITY_MAIL';
        });
    }

    public function test_search_with_pricing_options(): void
    {
        Http::fake([
            'apis.usps.com/shipments/v3/options/search' => Http::response([
                'options' => [],
            ]),
        ]);

        $this->client()->search([
            'originZIPCode' => '20500',
            'destinationZIPCode' => '33101',
            'weight' => 1.0,
            'length' => 6,
            'width' => 4,
            'height' => 2,
            'pricingOptions' => [
                ['priceType' => 'COMMERCIAL', 'paymentAccount' => '1234'],
            ],
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['pricingOptions'])
                && $body['pricingOptions'][0]['priceType'] === 'COMMERCIAL';
        });
    }

    public function test_search_handles_error(): void
    {
        Http::fake([
            'apis.usps.com/shipments/v3/options/search' => Http::response([
                'error' => ['code' => '400', 'message' => 'Invalid origin ZIP code.'],
            ], 400),
        ]);

        $client = $this->client();
        $client->search([
            'originZIPCode' => '00000',
            'destinationZIPCode' => '33101',
            'weight' => 1,
            'length' => 6,
            'width' => 4,
            'height' => 2,
        ]);

        $this->assertTrue($client->isError());
        $this->assertEquals('Invalid origin ZIP code.', $client->getErrorMessage());
    }

    public function test_search_throws_for_missing_required_field(): void
    {
        $this->expectException(\Johnpaulmedina\Usps\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Missing required field: weight.');

        $this->client()->search([
            'originZIPCode' => '20500',
            'destinationZIPCode' => '33101',
            'length' => 6,
            'width' => 4,
            'height' => 2,
        ]);
    }

    public function test_search_throws_for_negative_weight(): void
    {
        $this->expectException(\Johnpaulmedina\Usps\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('weight must be greater than 0');

        $this->client()->search([
            'originZIPCode' => '20500',
            'destinationZIPCode' => '33101',
            'weight' => -1,
            'length' => 6,
            'width' => 4,
            'height' => 2,
        ]);
    }

    public function test_search_normalizes_zip_codes(): void
    {
        Http::fake([
            'apis.usps.com/shipments/v3/options/search' => Http::response(['options' => []]),
        ]);

        $this->client()->search([
            'originZIPCode' => '205-00',
            'destinationZIPCode' => '331 01',
            'weight' => 1.0,
            'length' => 6,
            'width' => 4,
            'height' => 2,
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['originZIPCode'] === '20500'
                && $body['destinationZIPCode'] === '33101';
        });
    }

    public function test_search_throws_for_non_numeric_weight(): void
    {
        $this->expectException(\Johnpaulmedina\Usps\Exceptions\ValidationException::class);

        $this->client()->search([
            'originZIPCode' => '20500',
            'destinationZIPCode' => '33101',
            'weight' => 'abc',
            'length' => 6,
            'width' => 4,
            'height' => 2,
        ]);
    }
}
