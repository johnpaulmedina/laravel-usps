<?php

/**
 * USPS Service Standards API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class ServiceStandards extends USPSBase
{
    protected string $scope = 'service-standards';

    /**
     * Get delivery estimates between ZIP Codes.
     *
     * @param string $originZIPCode Origin ZIP (3, 5, or 9 digits)
     * @param string $destinationZIPCode Destination ZIP (5 or 9 digits)
     * @param array{acceptanceDate?: string, acceptanceTime?: string, mailClass?: string, destinationType?: string, serviceTypeCodes?: string, destinationEntryFacilityType?: string, weight?: float, weightUOM?: string, length?: float, height?: float, width?: float, dimensionsUOM?: string, rateHolderCRID?: string, presort?: bool} $options
     * @return array<string, mixed>
     */
    public function getEstimates(string $originZIPCode, string $destinationZIPCode, array $options = []): array
    {
        return $this->apiGet('/service-standards/v3/estimates', array_merge([
            'originZIPCode' => $originZIPCode,
            'destinationZIPCode' => $destinationZIPCode,
        ], $options));
    }

    /**
     * Get service standards (average delivery days) between ZIP Codes.
     *
     * @param string $originZIPCode Origin ZIP (3, 5, or 9 digits)
     * @param string $destinationZIPCode Destination ZIP (5 or 9 digits)
     * @param array{mailClass?: string, destinationType?: string, serviceTypeCodes?: string, destinationEntryFacilityType?: string, weight?: float, length?: float, height?: float, width?: float, dimensionsUOM?: string, rateHolderCRID?: string, presort?: bool} $options
     * @return array<string, mixed>
     */
    public function getStandards(string $originZIPCode, string $destinationZIPCode, array $options = []): array
    {
        return $this->apiGet('/service-standards/v3/standards', array_merge([
            'originZIPCode' => $originZIPCode,
            'destinationZIPCode' => $destinationZIPCode,
        ], $options));
    }
}
