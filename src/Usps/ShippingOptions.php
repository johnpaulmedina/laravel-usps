<?php

/**
 * USPS Shipping Options API v3
 * POST /shipments/v3/options/search
 *
 * Returns combined pricing, service standards, and available shipping
 * options for USPS products in a single request.
 *
 * @since  2.1
 * @author John Paul Medina
 * @see    https://github.com/USPS/api-examples
 */

namespace Johnpaulmedina\Usps;

class ShippingOptions extends USPSBase
{
    protected string $scope = 'shipments';

    /**
     * Search for available shipping options with pricing and delivery estimates.
     *
     * @param array{
     *   originZIPCode: string,
     *   destinationZIPCode: string,
     *   weight: float,
     *   length: float,
     *   width: float,
     *   height: float,
     *   mailClass?: string,
     *   processingCategory?: string,
     *   rateIndicator?: string,
     *   destinationEntryFacilityType?: string,
     *   priceType?: string,
     *   accountType?: string,
     *   accountNumber?: string,
     *   acceptanceDate?: string,
     * } $options
     * @return array<string, mixed>
     */
    public function search(array $options): array
    {
        $payload = [
            'originZIPCode' => $options['originZIPCode'] ?? '',
            'destinationZIPCode' => $options['destinationZIPCode'] ?? '',
            'weight' => $options['weight'] ?? 0,
            'length' => $options['length'] ?? 0,
            'width' => $options['width'] ?? 0,
            'height' => $options['height'] ?? 0,
        ];

        // Optional fields
        $optionalFields = [
            'mailClass', 'processingCategory', 'rateIndicator',
            'destinationEntryFacilityType', 'priceType', 'destinationType',
            'acceptanceDate', 'accountType', 'accountNumber',
        ];

        foreach ($optionalFields as $field) {
            if (isset($options[$field])) {
                $payload[$field] = $options[$field];
            }
        }

        // Pricing options (nested)
        if (isset($options['pricingOptions'])) {
            $payload['pricingOptions'] = $options['pricingOptions'];
        }

        return $this->apiPost('/shipments/v3/options/search', $payload);
    }
}
