<?php

/**
 * USPS City/State Lookup API v3
 * GET /addresses/v3/city-state
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class CityStateLookup extends USPSBase
{
    /**
     * Lookup city and state by ZIP code.
     */
    public function lookup(string $zipCode): array
    {
        return $this->apiGet('/addresses/v3/city-state', [
            'ZIPCode' => $zipCode,
        ]);
    }

    public function getPostFields(): array
    {
        return [];
    }
}
