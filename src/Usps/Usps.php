<?php

/**
 * Available Laravel Methods
 * Add other USPS API Methods
 * Based on Vincent Gabriel @VinceG USPS PHP-Api https://github.com/VinceG/USPS-php-api
 *
 * @since  1.0
 * @author John Paul Medina
 * @author Vincent Gabriel
 */

namespace Usps;

function __autoload($class_name) {
    include $class_name . '.php';
}

class Usps {

    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function validate($request) {

        $verify = new AddressVerify($this->config['username']);
        $address = new Address;
        $address->setFirmName(null);
        $address->setApt( (array_key_exists('Apartment', $request) ? $request['Apartment'] : null ) );
        $address->setAddress( (array_key_exists('Address', $request) ? $request['Address'] : null ) );
        $address->setCity( (array_key_exists('City', $request) ? $request['City'] : null ) );
        $address->setState( (array_key_exists('State', $request) ? $request['State'] : null ) );
        $address->setZip5( (array_key_exists('Zip', $request) ? $request['Zip'] : null ) );
        $address->setZip4('');

        // Add the address object to the address verify class
        $verify->addAddress($address);

        // Perform the request and return result
        $val1 = $verify->verify();
        $val2 = $verify->getArrayResponse();

        // var_dump($verify->isError());

        // See if it was successful
        if ($verify->isSuccess()) {
            return ['address' => $val2['AddressValidateResponse']['Address']];
        } else {
            return ['error' => $verify->getErrorMessage()];
        }


    }
}
