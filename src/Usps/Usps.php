<?php

/**
 * USPS API v3 — Laravel wrapper
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Usps
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function validate(array $request): array
    {
        $verify = new AddressVerify(
            $this->config['client_id'] ?? '',
            $this->config['client_secret'] ?? ''
        );

        $address = new Address;
        $address->setFirmName(null);
        $address->setApt($request['Apartment'] ?? $request['apartment'] ?? null);
        $address->setAddress($request['Address'] ?? $request['address'] ?? $request['street'] ?? null);
        $address->setCity($request['City'] ?? $request['city'] ?? null);
        $address->setState($request['State'] ?? $request['state'] ?? null);
        $address->setZip5($request['Zip'] ?? $request['zip'] ?? $request['zip5'] ?? null);
        $address->setZip4($request['zip4'] ?? '');

        $verify->addAddress($address);
        $verify->verify();

        if ($verify->isSuccess()) {
            return ['address' => $verify->getArrayResponse()['AddressValidateResponse']['Address']];
        }

        return ['error' => $verify->getErrorMessage()];
    }

    public function addressLookup(array $request): array
    {
        $lookup = new AddressVerify(
            $this->config['client_id'] ?? '',
            $this->config['client_secret'] ?? ''
        );

        $address = new Address;
        $address->setAddress($request['streetAddress'] ?? $request['address'] ?? '');
        $address->setApt($request['secondaryAddress'] ?? $request['apartment'] ?? '');
        $address->setCity($request['city'] ?? '');
        $address->setState($request['state'] ?? '');
        $address->setZip5($request['ZIPCode'] ?? $request['zip'] ?? '');

        $lookup->addAddress($address);

        return $lookup->verify();
    }

    public function cityStateLookup(string $zipCode): array
    {
        $lookup = new CityStateLookup(
            $this->config['client_id'] ?? '',
            $this->config['client_secret'] ?? ''
        );

        return $lookup->lookup($zipCode);
    }

    public function zipCodeLookup(array $request): array
    {
        $lookup = new ZipCodeLookup(
            $this->config['client_id'] ?? '',
            $this->config['client_secret'] ?? ''
        );

        $address = new Address;
        $address->setAddress($request['streetAddress'] ?? $request['address'] ?? '');
        $address->setApt($request['secondaryAddress'] ?? $request['apartment'] ?? '');
        $address->setCity($request['city'] ?? '');
        $address->setState($request['state'] ?? '');

        $lookup->addAddress($address);

        return $lookup->lookup();
    }
}
