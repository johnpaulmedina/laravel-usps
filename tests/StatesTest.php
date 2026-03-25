<?php

namespace Johnpaulmedina\Usps\Tests;

use Johnpaulmedina\Usps\States;
use PHPUnit\Framework\TestCase;

class StatesTest extends TestCase
{
    public function test_to_abbreviation_returns_null_for_unknown_long_string(): void
    {
        $this->assertNull(States::toAbbreviation('Narnia'));
        $this->assertNull(States::toAbbreviation('InvalidState'));
        $this->assertNull(States::toAbbreviation('xyz'));
    }

    public function test_to_abbreviation_preserves_valid_abbreviation(): void
    {
        $this->assertEquals('FL', States::toAbbreviation('FL'));
        $this->assertEquals('CA', States::toAbbreviation('ca'));
        $this->assertEquals('NY', States::toAbbreviation('ny'));
    }

    public function test_to_abbreviation_converts_full_name(): void
    {
        $this->assertEquals('FL', States::toAbbreviation('Florida'));
        $this->assertEquals('CA', States::toAbbreviation('california'));
        $this->assertEquals('NY', States::toAbbreviation('New York'));
        $this->assertEquals('DC', States::toAbbreviation('District of Columbia'));
    }

    public function test_to_abbreviation_handles_null_and_empty(): void
    {
        $this->assertNull(States::toAbbreviation(null));
        $this->assertEquals('', States::toAbbreviation(''));
    }

    public function test_to_full_name(): void
    {
        $this->assertEquals('Florida', States::toFullName('FL'));
        $this->assertEquals('California', States::toFullName('CA'));
    }

    public function test_is_valid(): void
    {
        $this->assertTrue(States::isValid('FL'));
        $this->assertTrue(States::isValid('CA'));
        $this->assertFalse(States::isValid('XX'));
        $this->assertFalse(States::isValid(null));
        $this->assertFalse(States::isValid('Florida'));
    }
}
