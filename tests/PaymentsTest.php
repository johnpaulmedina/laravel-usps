<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Payments;
use Orchestra\Testbench\TestCase;

class PaymentsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . hash('sha256', 'test-id_payments'), 'fake-token', 3600);
    }

    private function api(): Payments
    {
        return new Payments('test-id', 'test-secret');
    }

    public function test_create_payment_authorization(): void
    {
        Http::fake([
            'apis.usps.com/payments/v3/payment-authorization' => Http::response([
                'paymentAuthorizationToken' => 'jwt.token.here',
                'roles' => [['roleName' => 'PAYER']],
            ]),
        ]);

        $result = $this->api()->createPaymentAuthorization([
            'roles' => [
                ['roleName' => 'PAYER', 'CRID' => '12345678', 'accountType' => 'EPS', 'accountNumber' => '1234'],
                ['roleName' => 'LABEL_OWNER', 'CRID' => '12345678', 'MID' => '123456', 'manifestMID' => '123456'],
            ],
        ]);

        $this->assertArrayHasKey('paymentAuthorizationToken', $result);
    }

    public function test_get_payment_account(): void
    {
        Http::fake([
            'apis.usps.com/payments/v3/payment-account/*' => Http::response([
                'accountType' => 'EPS',
                'accountNumber' => '12345678',
                'nonProfitStatus' => false,
                'sufficientFunds' => true,
            ]),
        ]);

        $result = $this->api()->getPaymentAccount('12345678', 'EPS', ['amount' => 50.00]);
        $this->assertTrue($result['sufficientFunds']);
        $this->assertFalse($result['nonProfitStatus']);
    }
}
