<?php

/**
 * USPS City/State Lookup API v3
 * GET /addresses/v3/city-state
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Johnpaulmedina\Usps\Validation\ValidatesZipCodes;

class CityStateLookup extends USPSBase
{
    use ValidatesZipCodes;

    /**
     * Lookup city and state by ZIP code.
     */
    public function lookup(string $zipCode): array
    {
        $normalized = $this->normalizeZip5($zipCode);

        if ($normalized === null) {
            $this->errorCode = 1;
            $this->errorMessage = 'Invalid ZIP code format.';
            return [];
        }

        return $this->apiGet('/addresses/v3/city-state', [
            'ZIPCode' => $normalized,
        ]);
    }

    public function getPostFields(): array
    {
        return [];
    }
}
