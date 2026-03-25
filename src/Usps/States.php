<?php

/**
 * US State name/abbreviation helper.
 *
 * @since  2.1
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class States
{
    /** @var array<string, string> Full name → abbreviation */
    private const MAP = [
        'alabama' => 'AL', 'alaska' => 'AK', 'arizona' => 'AZ', 'arkansas' => 'AR',
        'california' => 'CA', 'colorado' => 'CO', 'connecticut' => 'CT', 'delaware' => 'DE',
        'district of columbia' => 'DC', 'florida' => 'FL', 'georgia' => 'GA', 'hawaii' => 'HI',
        'idaho' => 'ID', 'illinois' => 'IL', 'indiana' => 'IN', 'iowa' => 'IA',
        'kansas' => 'KS', 'kentucky' => 'KY', 'louisiana' => 'LA', 'maine' => 'ME',
        'maryland' => 'MD', 'massachusetts' => 'MA', 'michigan' => 'MI', 'minnesota' => 'MN',
        'mississippi' => 'MS', 'missouri' => 'MO', 'montana' => 'MT', 'nebraska' => 'NE',
        'nevada' => 'NV', 'new hampshire' => 'NH', 'new jersey' => 'NJ', 'new mexico' => 'NM',
        'new york' => 'NY', 'north carolina' => 'NC', 'north dakota' => 'ND', 'ohio' => 'OH',
        'oklahoma' => 'OK', 'oregon' => 'OR', 'pennsylvania' => 'PA', 'rhode island' => 'RI',
        'south carolina' => 'SC', 'south dakota' => 'SD', 'tennessee' => 'TN', 'texas' => 'TX',
        'utah' => 'UT', 'vermont' => 'VT', 'virginia' => 'VA', 'washington' => 'WA',
        'west virginia' => 'WV', 'wisconsin' => 'WI', 'wyoming' => 'WY',
        // Territories
        'american samoa' => 'AS', 'guam' => 'GU', 'marshall islands' => 'MH',
        'micronesia' => 'FM', 'northern mariana islands' => 'MP', 'palau' => 'PW',
        'puerto rico' => 'PR', 'virgin islands' => 'VI',
        // Military
        'armed forces americas' => 'AA', 'armed forces europe' => 'AE', 'armed forces pacific' => 'AP',
    ];

    /**
     * Convert a state name or abbreviation to the 2-letter abbreviation.
     * Returns the input unchanged if already a valid abbreviation.
     */
    public static function toAbbreviation(?string $state): ?string
    {
        if ($state === null || $state === '') {
            return $state;
        }

        $trimmed = trim($state);

        // Already a 2-letter abbreviation
        if (strlen($trimmed) === 2) {
            return strtoupper($trimmed);
        }

        $key = strtolower($trimmed);

        return self::MAP[$key] ?? null;
    }

    /**
     * Convert a 2-letter abbreviation to the full state name.
     */
    public static function toFullName(?string $abbreviation): ?string
    {
        if ($abbreviation === null || $abbreviation === '') {
            return $abbreviation;
        }

        $upper = strtoupper(trim($abbreviation));
        $flipped = array_flip(self::MAP);

        if (isset($flipped[$upper])) {
            return ucwords($flipped[$upper]);
        }

        return $abbreviation;
    }

    /**
     * Check if a value is a valid 2-letter state abbreviation.
     */
    public static function isValid(?string $state): bool
    {
        if ($state === null || strlen($state) !== 2) {
            return false;
        }

        return in_array(strtoupper($state), array_values(self::MAP), true);
    }
}
