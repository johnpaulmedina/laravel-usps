<?php

namespace Johnpaulmedina\Usps\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array validate(array $request)
 * @method static array addressLookup(array $request)
 * @method static array cityStateLookup(string $zipCode)
 * @method static array zipCodeLookup(array $request)
 * @method static \Johnpaulmedina\Usps\Tracking tracking()
 * @method static \Johnpaulmedina\Usps\Labels labels()
 * @method static \Johnpaulmedina\Usps\InternationalLabels internationalLabels()
 * @method static \Johnpaulmedina\Usps\DomesticPrices domesticPrices()
 * @method static \Johnpaulmedina\Usps\InternationalPrices internationalPrices()
 * @method static \Johnpaulmedina\Usps\ServiceStandards serviceStandards()
 * @method static \Johnpaulmedina\Usps\ServiceStandardsDirectory serviceStandardsDirectory()
 * @method static \Johnpaulmedina\Usps\ServiceStandardsFiles serviceStandardsFiles()
 * @method static \Johnpaulmedina\Usps\InternationalServiceStandards internationalServiceStandards()
 * @method static \Johnpaulmedina\Usps\Locations locations()
 * @method static \Johnpaulmedina\Usps\CarrierPickup carrierPickup()
 * @method static \Johnpaulmedina\Usps\Containers containers()
 * @method static \Johnpaulmedina\Usps\Payments payments()
 * @method static \Johnpaulmedina\Usps\Campaigns campaigns()
 * @method static \Johnpaulmedina\Usps\PackageCampaigns packageCampaigns()
 * @method static \Johnpaulmedina\Usps\Adjustments adjustments()
 * @method static \Johnpaulmedina\Usps\Disputes disputes()
 * @method static \Johnpaulmedina\Usps\Appointments appointments()
 * @method static \Johnpaulmedina\Usps\ShippingOptions shippingOptions()
 * @method static \Johnpaulmedina\Usps\ScanForm scanForms()
 *
 * @see \Johnpaulmedina\Usps\Usps
 */
class Usps extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'usps';
    }
}
