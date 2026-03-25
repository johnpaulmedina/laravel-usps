<?php

/**
 * USPS Payments API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Payments extends USPSBase
{
    protected string $scope = 'payments';

    /**
     * Create a payment authorization token for use with Labels API.
     *
     * @param array{roles: array<int, array{roleName: string, CRID?: string, MID?: string, manifestMID?: string, accountType?: string, accountNumber?: string, permitNumber?: string, permitZIP?: string}>} $parties
     * @return array<string, mixed>
     */
    public function createPaymentAuthorization(array $parties): array
    {
        return $this->apiPost('/payments/v3/payment-authorization', $parties);
    }

    /**
     * Inquire about a payment account (check funds, non-profit status).
     *
     * @param string $accountNumber
     * @param string $accountType EPS or PERMIT
     * @param array{permitZIPCode?: string, amount?: float} $options
     * @return array<string, mixed>
     */
    public function getPaymentAccount(string $accountNumber, string $accountType, array $options = []): array
    {
        return $this->apiGet("/payments/v3/payment-account/{$accountNumber}", array_merge([
            'accountType' => $accountType,
        ], $options));
    }
}
