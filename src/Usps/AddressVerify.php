<?php

/**
 * USPS Address Verification API v3
 * GET /addresses/v3/address
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Johnpaulmedina\Usps\Validation\ValidatesZipCodes;

class AddressVerify extends USPSBase
{
    use ValidatesZipCodes;
    protected array $addresses = [];

    public function addAddress(Address $address): self
    {
        $this->addresses[] = $address;
        return $this;
    }

    /**
     * Verify the first address added.
     */
    public function verify(): array
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
            'ZIPCode' => $address->getZip5(),
            'ZIPPlus4' => $address->getZip4() ?: null,
            'firm' => $address->getFirmName() ?: null,
        ]);

        return $this->apiGet('/addresses/v3/address', $query);
    }

    /**
     * Return the array response in a format compatible with the legacy API.
     */
    public function getArrayResponse(): array
    {
        $response = $this->getResponse();

        if ($this->isError() || !isset($response['address'])) {
            return $response;
        }

        $addr = $response['address'];

        $result = [
            'AddressValidateResponse' => [
                'Address' => [
                    'Address2' => $addr['streetAddress'] ?? '',
                    'Address1' => $addr['secondaryAddress'] ?? '',
                    'City' => $addr['city'] ?? '',
                    'State' => $addr['state'] ?? '',
                    'Zip5' => $addr['ZIPCode'] ?? '',
                    'Zip4' => $addr['ZIPPlus4'] ?? '',
                ],
            ],
        ];

        if (!empty($response['corrections'])) {
            $result['corrections'] = $response['corrections'];
        }

        if (!empty($response['additionalInfo'])) {
            $result['additionalInfo'] = $response['additionalInfo'];
        }

        if (!empty($response['warnings'])) {
            $result['warnings'] = $response['warnings'];
        }

        if (!empty($response['matches'])) {
            $result['matches'] = $response['matches'];
        }

        return $result;
    }

    /**
     * Legacy compat
     */
    public function getPostFields(): array
    {
        return $this->addresses;
    }
}
