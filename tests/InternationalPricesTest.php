<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\InternationalPrices;
use Orchestra\Testbench\TestCase;

class InternationalPricesTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_international-prices'), 'fake-token', 3600);
    }

    private function api(): InternationalPrices
    {
        return new InternationalPrices('test-id', 'test-secret');
    }

    public function test_base_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/base-rates/search' => Http::response(['totalBasePrice' => 28.50]),
        ]);

        $result = $this->api()->baseRateSearch(['destinationCountryCode' => 'CA', 'weight' => 2.0]);
        $this->assertArrayHasKey('totalBasePrice', $result);
    }

    public function test_extra_service_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/extra-service-rates/search' => Http::response(['price' => 15.00]),
        ]);

        $result = $this->api()->extraServiceRateSearch(['extraService' => '930']);
        $this->assertArrayHasKey('price', $result);
    }

    public function test_base_rate_list_search(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/base-rates-list/search' => Http::response(['rates' => []]),
        ]);

        $result = $this->api()->baseRateListSearch(['destinationCountryCode' => 'GB']);
        $this->assertArrayHasKey('rates', $result);
    }

    public function test_total_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/total-rates/search' => Http::response(['totalPrice' => 45.00]),
        ]);

        $result = $this->api()->totalRateSearch(['destinationCountryCode' => 'DE']);
        $this->assertArrayHasKey('totalPrice', $result);
    }

    public function test_letter_rate_search(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/letter-rates/search' => Http::response(['price' => 1.55]),
        ]);

        $result = $this->api()->letterRateSearch(['weight' => 1.0]);
        $this->assertArrayHasKey('price', $result);
    }

    public function test_base_rate_search_throws_for_negative_weight(): void
    {
        $this->expectException(\Johnpaulmedina\Usps\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('weight must be greater than 0');

        $this->api()->baseRateSearch(['destinationCountryCode' => 'CA', 'weight' => -1]);
    }

    public function test_base_rate_search_allows_missing_weight(): void
    {
        Http::fake([
            'apis.usps.com/international-prices/v3/base-rates/search' => Http::response(['totalBasePrice' => 10.00]),
        ]);

        $result = $this->api()->baseRateSearch(['destinationCountryCode' => 'CA']);
        $this->assertArrayHasKey('totalBasePrice', $result);
    }
}
