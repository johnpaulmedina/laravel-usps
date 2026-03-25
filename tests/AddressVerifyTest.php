<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Address;
use Johnpaulmedina\Usps\AddressVerify;
use Orchestra\Testbench\TestCase;

class AddressVerifyTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-client-id_addresses'), 'fake-token', 3600);
    }

    public function test_verify_returns_error_when_no_address_added(): void
    {
        $verify = new AddressVerify('test-client-id', 'test-secret');
        $verify->verify();

        $this->assertTrue($verify->isError());
        $this->assertEquals('No address provided.', $verify->getErrorMessage());
    }

    public function test_verify_sends_correct_query_params(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '1600 PENNSYLVANIA AVE NW',
                    'city' => 'WASHINGTON',
                    'state' => 'DC',
                    'ZIPCode' => '20500',
                    'ZIPPlus4' => '0005',
                ],
            ]),
        ]);

        $verify = new AddressVerify('test-client-id', 'test-secret');

        $address = new Address;
        $address->setAddress('1600 Pennsylvania Ave NW')
            ->setCity('Washington')
            ->setState('DC')
            ->setZip5('20500');

        $verify->addAddress($address);
        $verify->verify();

        $this->assertTrue($verify->isSuccess());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'addresses/v3/address')
                && $request->data()['streetAddress'] === '1600 Pennsylvania Ave NW'
                && $request->data()['city'] === 'Washington'
                && $request->data()['state'] === 'DC'
                && $request->data()['ZIPCode'] === '20500';
        });
    }

    public function test_verify_maps_response_to_legacy_format(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'address' => [
                    'streetAddress' => '1600 PENNSYLVANIA AVE NW',
                    'secondaryAddress' => '',
                    'city' => 'WASHINGTON',
                    'state' => 'DC',
                    'ZIPCode' => '20500',
                    'ZIPPlus4' => '0005',
                ],
            ]),
        ]);

        $verify = new AddressVerify('test-client-id', 'test-secret');

        $address = new Address;
        $address->setAddress('1600 Pennsylvania Ave NW')
            ->setState('DC')
            ->setZip5('20500');

        $verify->addAddress($address);
        $verify->verify();

        $result = $verify->getArrayResponse();

        $this->assertArrayHasKey('AddressValidateResponse', $result);
        $this->assertEquals('1600 PENNSYLVANIA AVE NW', $result['AddressValidateResponse']['Address']['Address2']);
        $this->assertEquals('WASHINGTON', $result['AddressValidateResponse']['Address']['City']);
        $this->assertEquals('DC', $result['AddressValidateResponse']['Address']['State']);
        $this->assertEquals('20500', $result['AddressValidateResponse']['Address']['Zip5']);
        $this->assertEquals('0005', $result['AddressValidateResponse']['Address']['Zip4']);
    }

    public function test_verify_handles_api_error(): void
    {
        Http::fake([
            'apis.usps.com/addresses/v3/address*' => Http::response([
                'error' => [
                    'code' => '404',
                    'message' => 'Address Not Found.',
                ],
            ], 404),
        ]);

        $verify = new AddressVerify('test-client-id', 'test-secret');

        $address = new Address;
        $address->setAddress('99999 Fake St')
            ->setState('XX')
            ->setZip5('00000');

        $verify->addAddress($address);
        $verify->verify();

        $this->assertTrue($verify->isError());
        $this->assertEquals('Address Not Found.', $verify->getErrorMessage());
    }
}
