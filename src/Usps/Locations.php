<?php

/**
 * USPS Locations API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Locations extends USPSBase
{
    protected string $scope = 'locations';

    /**
     * Lookup valid entry locations for destination entry parcels.
     *
     * @param string $destinationZIPCode 5-digit destination ZIP
     * @param array{destinationZIPPlus4?: string, mailClass?: string, processingCategory?: string, destinationEntryFacilityType?: string, sortType?: string, containerTopology?: string} $options
     * @return array<string, mixed>
     */
    public function getDropoffLocations(string $destinationZIPCode, array $options = []): array
    {
        return $this->apiGet('/locations/v3/dropoff-locations', array_merge([
            'destinationZIPCode' => $destinationZIPCode,
        ], $options));
    }

    /**
     * Get a list of post office locations based on search criteria.
     *
     * @param array{radius?: int, ZIPCode?: string, city?: string, state?: string, offset?: int, limit?: int} $options
     * @return array<string, mixed>
     */
    public function getPostOfficeLocations(array $options = []): array
    {
        return $this->apiGet('/locations/v3/post-office-locations', $options);
    }

    /**
     * Get a list of USPS Parcel Locker locations based on search criteria.
     *
     * @param array{radius?: int, ZIPCode?: string, city?: string, state?: string, offset?: int, limit?: int} $options
     * @return array<string, mixed>
     */
    public function getParcelLockerLocations(array $options = []): array
    {
        return $this->apiGet('/locations/v3/parcel-locker-locations', $options);
    }
}
