<?php

/**
 * USPS ZIP Code Lookup API v3
 * GET /addresses/v3/zipcode
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class ZipCodeLookup extends USPSBase
{
    protected array $addresses = [];

    public function addAddress(Address $address): self
    {
        $this->addresses[] = $address;
        return $this;
    }

    /**
     * Lookup ZIP code for the first address added.
     */
    public function lookup(): array
    {
        if (empty($this->addresses)) {
            $this->errorCode = 1;
            $this->errorMessage = 'No address provided.';
            return [];
        }

        $address = $this->addresses[0];

        $query = array_filter([
            'streetAddress' => $address->getAddress(),
            'secondaryAddress' => $address->getApt(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
        ]);

        return $this->apiGet('/addresses/v3/zipcode', $query);
    }

    public function getPostFields(): array
    {
        return $this->addresses;
    }
}
