<?php

/**
 * USPS API v3 -- Laravel wrapper
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Usps
{
    /** @var array{client_id: string, client_secret: string} */
    private array $config;

    /**
     * @param array{client_id: string, client_secret: string} $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    // -------------------------------------------------------------------------
    // Addresses (existing)
    // -------------------------------------------------------------------------

    /**
     * @param array<string, string|null> $request
     * @return array<string, mixed>
     */
    public function validate(array $request): array
    {
        $verify = new AddressVerify(
            $this->config['client_id'],
            $this->config['client_secret']
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
            $parsed = $verify->getArrayResponse();
            $result = ['address' => $parsed['AddressValidateResponse']['Address']];

            if (!empty($parsed['corrections'])) {
                $result['corrections'] = $parsed['corrections'];
            }
            if (!empty($parsed['additionalInfo'])) {
                $result['additionalInfo'] = $parsed['additionalInfo'];
            }
            if (!empty($parsed['warnings'])) {
                $result['warnings'] = $parsed['warnings'];
            }
            if (!empty($parsed['matches'])) {
                $result['matches'] = $parsed['matches'];
            }

            return $result;
        }

        return ['error' => $verify->getErrorMessage()];
    }

    /**
     * @param array<string, string> $request
     * @return array<string, mixed>
     */
    public function addressLookup(array $request): array
    {
        $lookup = new AddressVerify(
            $this->config['client_id'],
            $this->config['client_secret']
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

    /**
     * @return array<string, mixed>
     */
    public function cityStateLookup(string $zipCode): array
    {
        $lookup = new CityStateLookup(
            $this->config['client_id'],
            $this->config['client_secret']
        );

        return $lookup->lookup($zipCode);
    }

    /**
     * @param array<string, string> $request
     * @return array<string, mixed>
     */
    public function zipCodeLookup(array $request): array
    {
        $lookup = new ZipCodeLookup(
            $this->config['client_id'],
            $this->config['client_secret']
        );

        $address = new Address;
        $address->setAddress($request['streetAddress'] ?? $request['address'] ?? '');
        $address->setApt($request['secondaryAddress'] ?? $request['apartment'] ?? '');
        $address->setCity($request['city'] ?? '');
        $address->setState($request['state'] ?? '');

        $lookup->addAddress($address);

        return $lookup->lookup();
    }

    // -------------------------------------------------------------------------
    // Tracking
    // -------------------------------------------------------------------------

    public function tracking(): Tracking
    {
        return new Tracking($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Labels (Domestic)
    // -------------------------------------------------------------------------

    public function labels(): Labels
    {
        return new Labels($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // International Labels
    // -------------------------------------------------------------------------

    public function internationalLabels(): InternationalLabels
    {
        return new InternationalLabels($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Domestic Prices
    // -------------------------------------------------------------------------

    public function domesticPrices(): DomesticPrices
    {
        return new DomesticPrices($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // International Prices
    // -------------------------------------------------------------------------

    public function internationalPrices(): InternationalPrices
    {
        return new InternationalPrices($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Service Standards
    // -------------------------------------------------------------------------

    public function serviceStandards(): ServiceStandards
    {
        return new ServiceStandards($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Service Standards Directory
    // -------------------------------------------------------------------------

    public function serviceStandardsDirectory(): ServiceStandardsDirectory
    {
        return new ServiceStandardsDirectory($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Service Standards Files
    // -------------------------------------------------------------------------

    public function serviceStandardsFiles(): ServiceStandardsFiles
    {
        return new ServiceStandardsFiles($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // International Service Standards
    // -------------------------------------------------------------------------

    public function internationalServiceStandards(): InternationalServiceStandards
    {
        return new InternationalServiceStandards($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Locations
    // -------------------------------------------------------------------------

    public function locations(): Locations
    {
        return new Locations($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Carrier Pickup
    // -------------------------------------------------------------------------

    public function carrierPickup(): CarrierPickup
    {
        return new CarrierPickup($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Containers
    // -------------------------------------------------------------------------

    public function containers(): Containers
    {
        return new Containers($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Payments
    // -------------------------------------------------------------------------

    public function payments(): Payments
    {
        return new Payments($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Informed Delivery Campaigns
    // -------------------------------------------------------------------------

    public function campaigns(): Campaigns
    {
        return new Campaigns($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Informed Delivery Package Campaigns
    // -------------------------------------------------------------------------

    public function packageCampaigns(): PackageCampaigns
    {
        return new PackageCampaigns($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Adjustments
    // -------------------------------------------------------------------------

    public function adjustments(): Adjustments
    {
        return new Adjustments($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Disputes
    // -------------------------------------------------------------------------

    public function disputes(): Disputes
    {
        return new Disputes($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Appointments
    // -------------------------------------------------------------------------

    public function appointments(): Appointments
    {
        return new Appointments($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // Shipping Options
    // -------------------------------------------------------------------------

    public function shippingOptions(): ShippingOptions
    {
        return new ShippingOptions($this->config['client_id'], $this->config['client_secret']);
    }

    // -------------------------------------------------------------------------
    // SCAN Forms
    // -------------------------------------------------------------------------

    public function scanForms(): ScanForm
    {
        return new ScanForm($this->config['client_id'], $this->config['client_secret']);
    }
}
