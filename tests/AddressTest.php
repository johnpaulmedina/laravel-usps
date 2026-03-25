<?php

namespace Johnpaulmedina\Usps\Tests;

use Johnpaulmedina\Usps\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function test_set_and_get_address(): void
    {
        $address = new Address;
        $address->setAddress('1600 Pennsylvania Ave NW');

        $this->assertEquals('1600 Pennsylvania Ave NW', $address->getAddress());
    }

    public function test_set_and_get_apartment(): void
    {
        $address = new Address;
        $address->setApt('Suite 100');

        $this->assertEquals('Suite 100', $address->getApt());
    }

    public function test_set_and_get_city(): void
    {
        $address = new Address;
        $address->setCity('Washington');

        $this->assertEquals('Washington', $address->getCity());
    }

    public function test_set_and_get_state(): void
    {
        $address = new Address;
        $address->setState('DC');

        $this->assertEquals('DC', $address->getState());
    }

    public function test_set_and_get_zip5(): void
    {
        $address = new Address;
        $address->setZip5('20500');

        $this->assertEquals('20500', $address->getZip5());
    }

    public function test_set_and_get_zip4(): void
    {
        $address = new Address;
        $address->setZip4('0005');

        $this->assertEquals('0005', $address->getZip4());
    }

    public function test_set_and_get_firm_name(): void
    {
        $address = new Address;
        $address->setFirmName('The White House');

        $this->assertEquals('The White House', $address->getFirmName());
    }

    public function test_null_values_return_null(): void
    {
        $address = new Address;

        $this->assertNull($address->getAddress());
        $this->assertNull($address->getApt());
        $this->assertNull($address->getCity());
        $this->assertNull($address->getState());
        $this->assertNull($address->getZip5());
        $this->assertNull($address->getZip4());
        $this->assertNull($address->getFirmName());
    }

    public function test_fluent_interface(): void
    {
        $address = new Address;
        $result = $address->setAddress('123 Main St')
            ->setCity('Miami')
            ->setState('FL')
            ->setZip5('33101');

        $this->assertInstanceOf(Address::class, $result);
        $this->assertEquals('123 Main St', $address->getAddress());
        $this->assertEquals('Miami', $address->getCity());
        $this->assertEquals('FL', $address->getState());
        $this->assertEquals('33101', $address->getZip5());
    }

    public function test_get_address_info_returns_all_fields(): void
    {
        $address = new Address;
        $address->setAddress('123 Main St')
            ->setApt('Apt 4B')
            ->setCity('Miami')
            ->setState('FL')
            ->setZip5('33101')
            ->setZip4('1234');

        $info = $address->getAddressInfo();

        $this->assertArrayHasKey('Address2', $info);
        $this->assertArrayHasKey('Address1', $info);
        $this->assertArrayHasKey('City', $info);
        $this->assertArrayHasKey('State', $info);
        $this->assertArrayHasKey('Zip5', $info);
        $this->assertArrayHasKey('Zip4', $info);
    }
}
