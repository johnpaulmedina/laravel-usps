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

use Johnpaulmedina\Usps\Validation\ValidatesNumeric;
use Johnpaulmedina\Usps\Validation\ValidatesZipCodes;

class ShippingOptions extends USPSBase
{
    use ValidatesZipCodes;
    use ValidatesNumeric;

    protected string $scope = 'shipments';

    /**
     * Search for available shipping options with pricing and delivery estimates.
     *
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if required fields are missing or weight is invalid
     */
    public function search(array $options): array
    {
        $required = ['originZIPCode', 'destinationZIPCode', 'weight', 'length', 'width', 'height'];
        foreach ($required as $field) {
            if (!isset($options[$field]) || $options[$field] === '' || $options[$field] === null) {
                throw new \InvalidArgumentException("Missing required field: {$field}.");
            }
        }

        // Normalize ZIPs
        $originZip = preg_replace('/\D/', '', (string) $options['originZIPCode']);
        $destZip = preg_replace('/\D/', '', (string) $options['destinationZIPCode']);

        // Validate weight
        $this->validatePositiveFloat($options['weight'], 'weight');

        $payload = [
            'originZIPCode' => $originZip,
            'destinationZIPCode' => $destZip,
            'weight' => $options['weight'],
            'length' => $options['length'],
            'width' => $options['width'],
            'height' => $options['height'],
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
