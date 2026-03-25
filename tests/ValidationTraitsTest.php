<?php

namespace Johnpaulmedina\Usps\Tests;

use Johnpaulmedina\Usps\Validation\ValidatesDates;
use Johnpaulmedina\Usps\Validation\ValidatesNumeric;
use Johnpaulmedina\Usps\Validation\ValidatesZipCodes;
use PHPUnit\Framework\TestCase;

class ValidationTraitsTest extends TestCase
{
    // --- ValidatesZipCodes ---

    private function zipHelper(): object
    {
        return new class {
            use ValidatesZipCodes {
                normalizeZip5 as public;
                normalizeZip4 as public;
                splitZip as public;
            }
        };
    }

    public function test_normalize_zip5_valid(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals('20500', $h->normalizeZip5('20500'));
        $this->assertEquals('33101', $h->normalizeZip5('33101'));
    }

    public function test_normalize_zip5_strips_dashes_and_spaces(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals('20500', $h->normalizeZip5('205-00'));
        $this->assertEquals('20500', $h->normalizeZip5('205 00'));
    }

    public function test_normalize_zip5_returns_null_for_invalid(): void
    {
        $h = $this->zipHelper();
        $this->assertNull($h->normalizeZip5('abc'));
        $this->assertNull($h->normalizeZip5('123'));
        $this->assertNull($h->normalizeZip5('1234567'));
        $this->assertNull($h->normalizeZip5(null));
        $this->assertNull($h->normalizeZip5(''));
    }

    public function test_normalize_zip4_valid(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals('0005', $h->normalizeZip4('0005'));
        $this->assertEquals('1234', $h->normalizeZip4('1234'));
    }

    public function test_normalize_zip4_returns_null_for_invalid(): void
    {
        $h = $this->zipHelper();
        $this->assertNull($h->normalizeZip4('abc'));
        $this->assertNull($h->normalizeZip4('12345'));
        $this->assertNull($h->normalizeZip4(null));
        $this->assertNull($h->normalizeZip4(''));
    }

    public function test_split_zip_dash_format(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals(['20500', '0005'], $h->splitZip('20500-0005'));
    }

    public function test_split_zip_nine_digit(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals(['20500', '0005'], $h->splitZip('205000005'));
    }

    public function test_split_zip_five_digit(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals(['20500', null], $h->splitZip('20500'));
    }

    public function test_split_zip_invalid(): void
    {
        $h = $this->zipHelper();
        $this->assertEquals([null, null], $h->splitZip('abc'));
    }

    // --- ValidatesDates ---

    private function dateHelper(): object
    {
        return new class {
            use ValidatesDates {
                normalizeDate as public;
            }
        };
    }

    public function test_normalize_date_valid(): void
    {
        $h = $this->dateHelper();
        $this->assertEquals('2026-03-24', $h->normalizeDate('2026-03-24'));
        $this->assertEquals('2026-03-24', $h->normalizeDate('March 24, 2026'));
        $this->assertEquals('2026-03-24', $h->normalizeDate('03/24/2026'));
    }

    public function test_normalize_date_null_and_empty(): void
    {
        $h = $this->dateHelper();
        $this->assertNull($h->normalizeDate(null));
        $this->assertNull($h->normalizeDate(''));
        $this->assertNull($h->normalizeDate('   '));
    }

    public function test_normalize_date_invalid(): void
    {
        $h = $this->dateHelper();
        $this->assertNull($h->normalizeDate('not-a-date'));
    }

    // --- ValidatesNumeric ---

    private function numericHelper(): object
    {
        return new class {
            use ValidatesNumeric {
                validatePositiveFloat as public;
            }
        };
    }

    public function test_validate_positive_float_valid(): void
    {
        $h = $this->numericHelper();
        $this->assertEquals(2.5, $h->validatePositiveFloat(2.5, 'weight'));
        $this->assertEquals(1.0, $h->validatePositiveFloat('1', 'weight'));
        $this->assertEquals(0.5, $h->validatePositiveFloat(0.5, 'weight'));
    }

    public function test_validate_positive_float_throws_for_non_numeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('weight must be numeric');

        $this->numericHelper()->validatePositiveFloat('abc', 'weight');
    }

    public function test_validate_positive_float_throws_for_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('weight must be greater than 0');

        $this->numericHelper()->validatePositiveFloat(0, 'weight');
    }

    public function test_validate_positive_float_throws_for_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('weight must be greater than 0');

        $this->numericHelper()->validatePositiveFloat(-5, 'weight');
    }
}
