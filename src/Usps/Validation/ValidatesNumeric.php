<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Validation;

trait ValidatesNumeric
{
    /**
     * Validate and cast a value to a positive float.
     *
     * @throws \InvalidArgumentException if value is not numeric or <= 0
     */
    protected function validatePositiveFloat(mixed $value, string $field): float
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("{$field} must be numeric, got " . gettype($value) . ".");
        }

        $float = (float) $value;

        if ($float <= 0) {
            throw new \InvalidArgumentException("{$field} must be greater than 0, got {$float}.");
        }

        return $float;
    }
}
