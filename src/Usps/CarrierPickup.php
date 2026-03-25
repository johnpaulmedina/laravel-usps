<?php

/**
 * USPS Carrier Pickup API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class CarrierPickup extends USPSBase
{
    protected string $scope = 'carrier-pickup';

    /**
     * Check carrier pickup service availability at an address.
     *
     * @param string $streetAddress
     * @param array{secondaryAddress?: string, city?: string, state?: string, ZIPCode?: string, ZIPPlus4?: string, urbanization?: string} $options
     * @return array<string, mixed>
     */
    public function checkEligibility(string $streetAddress, array $options = []): array
    {
        return $this->apiGet('/pickup/v3/carrier-pickup/eligibility', array_merge([
            'streetAddress' => $streetAddress,
        ], $options));
    }

    /**
     * Schedule a carrier pickup.
     *
     * @param array{pickupDate: string, pickupAddress: array<string, mixed>, packages: array<int, array{packageType: string, packageCount: int}>, estimatedWeight: float, pickupLocation: array{packageLocation: string, specialInstructions?: string, dogPresent?: bool}, nextAvailablePickup?: bool} $request
     * @return array<string, mixed>
     */
    public function schedulePickup(array $request): array
    {
        return $this->apiPost('/pickup/v3/carrier-pickup', $request);
    }

    /**
     * Get a previously scheduled carrier pickup by confirmation number.
     *
     * @param string $confirmationNumber
     * @return array<string, mixed>
     */
    public function getPickup(string $confirmationNumber): array
    {
        return $this->apiGet("/pickup/v3/carrier-pickup/{$confirmationNumber}");
    }

    /**
     * Update a previously scheduled carrier pickup.
     *
     * @param string $confirmationNumber
     * @param array<string, mixed> $updateData
     * @param string $etag The ETag from a previous GET
     * @return array<string, mixed>
     */
    public function updatePickup(string $confirmationNumber, array $updateData, string $etag): array
    {
        return $this->apiPut("/pickup/v3/carrier-pickup/{$confirmationNumber}", $updateData, [
            'If-Match' => $etag,
        ]);
    }

    /**
     * Cancel a previously scheduled carrier pickup.
     *
     * @param string $confirmationNumber
     * @param string $etag The ETag from a previous GET
     * @return array<string, mixed>
     */
    public function cancelPickup(string $confirmationNumber, string $etag): array
    {
        return $this->apiDelete("/pickup/v3/carrier-pickup/{$confirmationNumber}", [], [
            'If-Match' => $etag,
        ]);
    }
}
