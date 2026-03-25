<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Validation;

trait ValidatesDates
{
    /**
     * Normalize any recognizable date format to YYYY-MM-DD.
     * Returns null if the input is null, empty, or not a valid date.
     */
    protected function normalizeDate(?string $date): ?string
    {
        if ($date === null || trim($date) === '') {
            return null;
        }

        $date = trim($date);

        try {
            $dt = new \DateTimeImmutable($date);
            return $dt->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
