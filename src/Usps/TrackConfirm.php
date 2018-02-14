<?php

/**
 * Based on Vincent Gabriel @VinceG USPS PHP-Api https://github.com/VinceG/USPS-php-api
 *
 * @since  1.0
 * @author John Paul Medina
 * @author Vincent Gabriel
 */

namespace Johnpaulmedina\Usps;

class TrackConfirm extends USPSBase
{
    /**
     * @var string - the api version used for this type of call
     */
    protected $apiVersion = 'TrackV2';
    /**
     * @var array - additional request parameters for Revision 1
     */
    protected $requestData = [];
    /**
     * @var array - list of all packages added so far
     */
    protected $packages = [];

    public function getEndpoint()
    {
        return self::$testMode ? 'http://production.shippingapis.com/ShippingAPITest.dll' : 'http://production.shippingapis.com/ShippingAPI.dll';
    }

    /**
     * Perform the API call
     *
     * @return string
     */
    public function getTracking()
    {
        return $this->doRequest();
    }

    /**
     * returns array of all packages added so far
     *
     * @return array
     */
    public function getPostFields()
    {
        return array_merge($this->requestData, $this->packages);
    }

    /**
     * Add Package to the stack
     *
     * @param string $id the address unique id
     * @return void
     */
    public function addPackage($id)
    {
        $this->packages['TrackID'][] = ['@attributes' => ['ID' => $id]];
    }

    /**
     * Set revision ID and additional required fields
     *
     * @param string $clientIp
     * @param string $sourceId
     * @param int $revisionId
     * @return void
     */
    public function setRevision($clientIp, $sourceId, $revisionId = 1)
    {
        $this->requestData['Revision'] = $revisionId;
        $this->requestData['ClientIp'] = $clientIp;
        $this->requestData['SourceId'] = $sourceId;
    }
}
