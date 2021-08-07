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

namespace Johnpaulmedina\Usps;

use Johnpaulmedina\Usps\Exceptions\UspsTrackConfirmException;

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

    /**
     * @param $ids array|string
     * @param $sourceId null|string
     *
     * @return array
     * @throws UspsTrackConfirmException
     */
    public function trackConfirm($ids, $sourceId = null)
    {
        $trackConfirm = new TrackConfirm($this->config['username']);
        $trackConfirm->setTestMode(empty($this->config['testmode']) ? false : true);

        if ($sourceId) {
            // Assume revision 1 tracking is desired when sourceId supplied
            $trackConfirm->setRevision(request()->getClientIp(), $sourceId);
        }

        collect(is_array($ids)? $ids : [ $ids ])->each(function ($id) use ($trackConfirm) {
            $trackConfirm->addPackage($id);
        });

        $trackConfirm->getTracking();
        if ($trackConfirm->isError()) {
            throw new UspsTrackConfirmException($trackConfirm->getErrorMessage(), $trackConfirm->getErrorCode());
        }

        return $trackConfirm->getArrayResponse();
    }
    
    public function rate($request){
        $rate = new Rate($this->config['username']);
        $ratepackage = new RatePackage();
        $ratepackage->setService((array_key_exists('Service', $request) ? $request['Service'] : null );
         $ratepackage->setFirstClassMailType((array_key_exists('FirstClassMailType', $request) ? $request['FirstClassMailType'] : null ));
         $ratepackage->setZipOrigination((array_key_exists('ZipOrigination', $request) ? $request['ZipOrigination'] : null ));
         $ratepackage->setZipDestination($(array_key_exists('ZipDestination', $request) ? $request['ZipDestination'] : null ));
         $ratepackage->setPounds((array_key_exists('Pounds', $request) ? $request['Pounds'] : null ));
         $ratepackage->setOunces((array_key_exists('Ounces', $request) ? $request['Ounces'] : null ));
         $ratepackage->setContainer((array_key_exists('Container', $request) ? $request['Container'] : null ));
         $ratepackage->setSize((array_key_exists('Size', $request) ? $request['Size'] : null ));
        
        // Add the Package object to the Rate Package class
        $rate->addPackage($ratepackage);
        
          // Perform the request and return result
        $val1 = $rate->getRate();
        $val2 = $rate->getArrayResponse();

        // var_dump($verify->isError());

        // See if it was successful
        if ($rate->isSuccess()) {
            return ['rate' => $val2['RateValidateResponse']['Rate']];
        } else {
            return ['error' => $verify->getErrorMessage()];
        }
            
        
    }
}
