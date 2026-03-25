<?php

/**
 * USPS Adjustments API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Adjustments extends USPSBase
{
    protected string $scope = 'adjustments';

    /**
     * Get shipping adjustments for a tracking number.
     *
     * @param string $crid Customer Registration ID
     * @param string $trackingNumber Package tracking number (PIC)
     * @param string $eventType One of: CENSUS, DUPLICATES, UNMANIFESTED
     * @param array{destinationZIPCode?: string} $options
     * @return array<string, mixed>
     */
    public function getAdjustments(string $crid, string $trackingNumber, string $eventType, array $options = []): array
    {
        return $this->apiGet(
            "/adjustments/v3/adjustments/{$crid}/{$trackingNumber}/{$eventType}",
            $options
        );
    }
}
