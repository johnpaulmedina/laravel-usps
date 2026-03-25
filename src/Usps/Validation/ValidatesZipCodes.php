<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Validation;

trait ValidatesZipCodes
{
    /**
     * Normalize a 5-digit ZIP code by stripping dashes and spaces.
     * Returns null if the result is not exactly 5 digits.
     */
    protected function normalizeZip5(?string $zip): ?string
    {
        if ($zip === null || $zip === '') {
            return null;
        }

        $cleaned = preg_replace('/[\s\-]/', '', $zip);

        if ($cleaned === null || !preg_match('/^\d{5}$/', $cleaned)) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Normalize a 4-digit ZIP+4 extension by stripping dashes and spaces.
     * Returns null if the result is not exactly 4 digits.
     */
    protected function normalizeZip4(?string $zip): ?string
    {
        if ($zip === null || $zip === '') {
            return null;
        }

        $cleaned = preg_replace('/[\s\-]/', '', $zip);

        if ($cleaned === null || !preg_match('/^\d{4}$/', $cleaned)) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Split a ZIP string like '20500-0005' into ['20500', '0005'].
     * Also handles '205000005' (9 digits) and plain '20500'.
     *
     * @return array{0: string|null, 1: string|null}
     */
    protected function splitZip(string $zip): array
    {
        $cleaned = preg_replace('/[\s]/', '', $zip);

        if ($cleaned === null) {
            return [null, null];
        }

        if (preg_match('/^(\d{5})-(\d{4})$/', $cleaned, $matches)) {
            return [$matches[1], $matches[2]];
        }

        if (preg_match('/^(\d{5})(\d{4})$/', $cleaned, $matches)) {
            return [$matches[1], $matches[2]];
        }

        if (preg_match('/^\d{5}$/', $cleaned)) {
            return [$cleaned, null];
        }

        return [null, null];
    }
}
