<?php

namespace Johnpaulmedina\Usps\Tests;

use Johnpaulmedina\Usps\Usps;
use Johnpaulmedina\Usps\Tracking;
use Johnpaulmedina\Usps\Labels;
use Johnpaulmedina\Usps\InternationalLabels;
use Johnpaulmedina\Usps\DomesticPrices;
use Johnpaulmedina\Usps\InternationalPrices;
use Johnpaulmedina\Usps\ServiceStandards;
use Johnpaulmedina\Usps\ServiceStandardsDirectory;
use Johnpaulmedina\Usps\ServiceStandardsFiles;
use Johnpaulmedina\Usps\InternationalServiceStandards;
use Johnpaulmedina\Usps\Locations;
use Johnpaulmedina\Usps\CarrierPickup;
use Johnpaulmedina\Usps\Containers;
use Johnpaulmedina\Usps\Payments;
use Johnpaulmedina\Usps\Campaigns;
use Johnpaulmedina\Usps\PackageCampaigns;
use Johnpaulmedina\Usps\Adjustments;
use Johnpaulmedina\Usps\Disputes;
use Johnpaulmedina\Usps\Appointments;
use Johnpaulmedina\Usps\ShippingOptions;
use Johnpaulmedina\Usps\ScanForm;
use Orchestra\Testbench\TestCase;

class FacadeTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    private function usps(): Usps
    {
        return new Usps(['client_id' => 'test', 'client_secret' => 'secret']);
    }

    public function test_all_api_accessors_return_correct_types(): void
    {
        $usps = $this->usps();

        $this->assertInstanceOf(Tracking::class, $usps->tracking());
        $this->assertInstanceOf(Labels::class, $usps->labels());
        $this->assertInstanceOf(InternationalLabels::class, $usps->internationalLabels());
        $this->assertInstanceOf(DomesticPrices::class, $usps->domesticPrices());
        $this->assertInstanceOf(InternationalPrices::class, $usps->internationalPrices());
        $this->assertInstanceOf(ServiceStandards::class, $usps->serviceStandards());
        $this->assertInstanceOf(ServiceStandardsDirectory::class, $usps->serviceStandardsDirectory());
        $this->assertInstanceOf(ServiceStandardsFiles::class, $usps->serviceStandardsFiles());
        $this->assertInstanceOf(InternationalServiceStandards::class, $usps->internationalServiceStandards());
        $this->assertInstanceOf(Locations::class, $usps->locations());
        $this->assertInstanceOf(CarrierPickup::class, $usps->carrierPickup());
        $this->assertInstanceOf(Containers::class, $usps->containers());
        $this->assertInstanceOf(Payments::class, $usps->payments());
        $this->assertInstanceOf(Campaigns::class, $usps->campaigns());
        $this->assertInstanceOf(PackageCampaigns::class, $usps->packageCampaigns());
        $this->assertInstanceOf(Adjustments::class, $usps->adjustments());
        $this->assertInstanceOf(Disputes::class, $usps->disputes());
        $this->assertInstanceOf(Appointments::class, $usps->appointments());
        $this->assertInstanceOf(ShippingOptions::class, $usps->shippingOptions());
        $this->assertInstanceOf(ScanForm::class, $usps->scanForms());
    }
}
