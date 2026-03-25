<?php

/**
 * USPS Mailer Review Disputes API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Disputes extends USPSBase
{
    protected string $scope = 'disputes';

    /**
     * Create a dispute request for USPS pricing adjustments.
     *
     * @param array{EPSTransactionID: string, trackingID: string, CRID: string, reason: string, description: string, name: string, disputeCount: string} $disputeData
     * @return array<string, mixed>
     */
    public function createDispute(array $disputeData): array
    {
        return $this->apiPost('/disputes/v3/dispute', $disputeData);
    }
}
