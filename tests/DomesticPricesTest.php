<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\DomesticPrices;
use Orchestra\Testbench\TestCase;

class DomesticPricesTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_prices'), 'fake-token', 3600);
    }

    private function api(): DomesticPrices
    {
        return new DomesticPrices('test-id', 'test-secret');
    }

    public function test_base_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/base-rates/search' => Http::response(['totalBasePrice' => 7.64]),
        ]);

        $result = $this->api()->baseRateSearch(['originZIPCode' => '20500', 'destinationZIPCode' => '33101', 'weight' => 2.5]);
        $this->assertArrayHasKey('totalBasePrice', $result);
    }

    public function test_extra_service_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/extra-service-rates/search' => Http::response(['price' => 3.50]),
        ]);

        $result = $this->api()->extraServiceRateSearch(['extraService' => '920', 'mailClass' => 'PRIORITY_MAIL']);
        $this->assertArrayHasKey('price', $result);
    }

    public function test_base_rate_list_search(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/base-rates-list/search' => Http::response(['rates' => []]),
        ]);

        $result = $this->api()->baseRateListSearch(['originZIPCode' => '20500']);
        $this->assertArrayHasKey('rates', $result);
    }

    public function test_total_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/total-rates/search' => Http::response(['totalPrice' => 11.14]),
        ]);

        $result = $this->api()->totalRateSearch(['originZIPCode' => '20500']);
        $this->assertArrayHasKey('totalPrice', $result);
    }

    public function test_letter_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/prices/v3/letter-rates/search' => Http::response(['price' => 0.68]),
        ]);

        $result = $this->api()->letterRateSearch(['weight' => 1.0]);
        $this->assertArrayHasKey('price', $result);
    }
}
