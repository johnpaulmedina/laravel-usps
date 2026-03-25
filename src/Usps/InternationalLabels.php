<?php

/**
 * USPS International Labels API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class InternationalLabels extends USPSBase
{
    protected string $scope = 'international-labels';

    /**
     * Create an international shipping label.
     *
     * @param array<string, mixed> $labelData
     * @param string $paymentToken Payment authorization token
     * @param string|null $idempotencyKey Optional idempotency key
     * @return array<string, mixed>
     */
    public function createLabel(array $labelData, string $paymentToken, ?string $idempotencyKey = null): array
    {
        $headers = ['X-Payment-Authorization-Token' => $paymentToken];
        if ($idempotencyKey !== null) {
            $headers['X-Idempotency-Key'] = $idempotencyKey;
        }

        return $this->apiPost('/international-labels/v3/international-label', $labelData, $headers);
    }

    /**
     * Reprint an international shipping label.
     *
     * @param string $trackingNumber
     * @param array<string, mixed> $reprintData
     * @param string $paymentToken Payment authorization token
     * @return array<string, mixed>
     */
    public function reprintLabel(string $trackingNumber, array $reprintData, string $paymentToken): array
    {
        return $this->apiPost("/international-labels/v3/international-label-reprint/{$trackingNumber}", $reprintData, [
            'X-Payment-Authorization-Token' => $paymentToken,
        ]);
    }

    /**
     * Cancel an international label or request a refund.
     *
     * @param string $trackingNumber
     * @return array<string, mixed>
     */
    public function cancelLabel(string $trackingNumber): array
    {
        return $this->apiDelete("/international-labels/v3/international-label/{$trackingNumber}");
    }

    /**
     * Create a First-Class Mail International letter, flat, or card indicia.
     *
     * @param array<string, mixed> $indiciaData
     * @param string $paymentToken Payment authorization token
     * @return array<string, mixed>
     */
    public function createIndicia(array $indiciaData, string $paymentToken): array
    {
        return $this->apiPost('/international-labels/v3/indicia', $indiciaData, [
            'X-Payment-Authorization-Token' => $paymentToken,
        ]);
    }
}
