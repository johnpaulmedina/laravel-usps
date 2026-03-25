<?php

/**
 * USPS Service Standards API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Johnpaulmedina\Usps\Validation\ValidatesZipCodes;

class ServiceStandards extends USPSBase
{
    use ValidatesZipCodes;

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
        $origin = $this->normalizeZipLoose($originZIPCode);
        $destination = $this->normalizeZipLoose($destinationZIPCode);

        return $this->apiGet('/service-standards/v3/estimates', array_merge([
            'originZIPCode' => $origin,
            'destinationZIPCode' => $destination,
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
        $origin = $this->normalizeZipLoose($originZIPCode);
        $destination = $this->normalizeZipLoose($destinationZIPCode);

        return $this->apiGet('/service-standards/v3/standards', array_merge([
            'originZIPCode' => $origin,
            'destinationZIPCode' => $destination,
        ], $options));
    }

    /**
     * Normalize a ZIP code loosely: strip non-digits but allow 3, 5, or 9 digit formats.
     * Returns the cleaned string (does not reject unknown lengths to stay backward compatible).
     */
    private function normalizeZipLoose(string $zip): string
    {
        return preg_replace('/\D/', '', $zip) ?: $zip;
    }
}
