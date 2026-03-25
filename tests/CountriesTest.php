<?php

namespace Johnpaulmedina\Usps\Tests;

use Johnpaulmedina\Usps\Countries;
use PHPUnit\Framework\TestCase;

class CountriesTest extends TestCase
{
    public function test_to_code_with_full_name(): void
    {
        $this->assertEquals('US', Countries::toCode('United States'));
        $this->assertEquals('CA', Countries::toCode('Canada'));
        $this->assertEquals('GB', Countries::toCode('United Kingdom'));
        $this->assertEquals('DE', Countries::toCode('Germany'));
        $this->assertEquals('FR', Countries::toCode('France'));
        $this->assertEquals('JP', Countries::toCode('Japan'));
        $this->assertEquals('CN', Countries::toCode('China'));
        $this->assertEquals('IN', Countries::toCode('India'));
        $this->assertEquals('BR', Countries::toCode('Brazil'));
        $this->assertEquals('AU', Countries::toCode('Australia'));
        $this->assertEquals('MX', Countries::toCode('Mexico'));
    }

    public function test_to_code_case_insensitive(): void
    {
        $this->assertEquals('US', Countries::toCode('united states'));
        $this->assertEquals('CA', Countries::toCode('CANADA'));
        $this->assertEquals('GB', Countries::toCode('United Kingdom'));
    }

    public function test_to_code_with_valid_two_letter_code(): void
    {
        $this->assertEquals('US', Countries::toCode('US'));
        $this->assertEquals('CA', Countries::toCode('CA'));
        $this->assertEquals('GB', Countries::toCode('GB'));
        $this->assertEquals('DE', Countries::toCode('de'));
    }

    public function test_to_code_with_invalid_two_letter_code(): void
    {
        $this->assertNull(Countries::toCode('ZZ'));
        $this->assertNull(Countries::toCode('XX'));
    }

    public function test_to_code_with_common_short_forms(): void
    {
        $this->assertEquals('GB', Countries::toCode('UK'));
        $this->assertEquals('US', Countries::toCode('USA'));
        $this->assertEquals('AE', Countries::toCode('UAE'));
    }

    public function test_to_code_with_null_and_empty(): void
    {
        $this->assertNull(Countries::toCode(null));
        $this->assertNull(Countries::toCode(''));
        $this->assertNull(Countries::toCode('   '));
    }

    public function test_to_code_with_unknown_name(): void
    {
        $this->assertNull(Countries::toCode('Narnia'));
        $this->assertNull(Countries::toCode('Middle Earth'));
    }

    public function test_to_code_trims_whitespace(): void
    {
        $this->assertEquals('US', Countries::toCode('  United States  '));
        $this->assertEquals('CA', Countries::toCode(' CA '));
    }

    public function test_to_code_with_alternate_names(): void
    {
        $this->assertEquals('CZ', Countries::toCode('Czechia'));
        $this->assertEquals('CZ', Countries::toCode('Czech Republic'));
        $this->assertEquals('TR', Countries::toCode('Turkey'));
        $this->assertEquals('TR', Countries::toCode('Turkiye'));
    }

    public function test_is_valid_with_valid_codes(): void
    {
        $this->assertTrue(Countries::isValid('US'));
        $this->assertTrue(Countries::isValid('CA'));
        $this->assertTrue(Countries::isValid('GB'));
        $this->assertTrue(Countries::isValid('de'));
    }

    public function test_is_valid_with_invalid_codes(): void
    {
        $this->assertFalse(Countries::isValid('ZZ'));
        $this->assertFalse(Countries::isValid('XX'));
        $this->assertFalse(Countries::isValid(null));
        $this->assertFalse(Countries::isValid('USA'));
        $this->assertFalse(Countries::isValid('U'));
    }

    public function test_is_valid_trims_whitespace(): void
    {
        $this->assertTrue(Countries::isValid(' US '));
    }

    public function test_has_at_least_50_countries(): void
    {
        $reflection = new \ReflectionClass(Countries::class);
        $constants = $reflection->getReflectionConstants();
        $map = null;
        foreach ($constants as $const) {
            if ($const->getName() === 'MAP') {
                $map = $const->getValue();
                break;
            }
        }

        $this->assertNotNull($map);
        // Unique country codes (some names map to same code like Czechia/Czech Republic)
        $uniqueCodes = array_unique(array_values($map));
        $this->assertGreaterThanOrEqual(50, count($uniqueCodes));
    }
}
