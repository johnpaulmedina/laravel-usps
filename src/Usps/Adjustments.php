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

    /** @var string[] Valid event type values */
    private const VALID_EVENT_TYPES = ['CENSUS', 'DUPLICATES', 'UNMANIFESTED'];

    /**
     * Get shipping adjustments for a tracking number.
     *
     * @param string $crid Customer Registration ID
     * @param string $trackingNumber Package tracking number (PIC)
     * @param string $eventType One of: CENSUS, DUPLICATES, UNMANIFESTED
     * @param array{destinationZIPCode?: string} $options
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if eventType is invalid
     */
    public function getAdjustments(string $crid, string $trackingNumber, string $eventType, array $options = []): array
    {
        $eventType = strtoupper(trim($eventType));

        if (!in_array($eventType, self::VALID_EVENT_TYPES, true)) {
            throw new \InvalidArgumentException(
                "Invalid eventType: {$eventType}. Must be one of: " . implode(', ', self::VALID_EVENT_TYPES) . "."
            );
        }

        $crid = rawurlencode($crid);
        $trackingNumber = rawurlencode($trackingNumber);
        $eventType = rawurlencode($eventType);

        return $this->apiGet(
            "/adjustments/v3/adjustments/{$crid}/{$trackingNumber}/{$eventType}",
            $options
        );
    }
}
