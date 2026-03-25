<?php

/**
 * ISO 3166-1 alpha-2 country code helper.
 *
 * @since  2.1
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Countries
{
    /** @var array<string, string> Lowercase name → ISO 3166-1 alpha-2 code */
    private const MAP = [
        'afghanistan' => 'AF', 'albania' => 'AL', 'algeria' => 'DZ', 'argentina' => 'AR',
        'australia' => 'AU', 'austria' => 'AT', 'bahamas' => 'BS', 'bahrain' => 'BH',
        'bangladesh' => 'BD', 'barbados' => 'BB', 'belgium' => 'BE', 'belize' => 'BZ',
        'bermuda' => 'BM', 'bolivia' => 'BO', 'brazil' => 'BR', 'brunei' => 'BN',
        'bulgaria' => 'BG', 'cambodia' => 'KH', 'canada' => 'CA', 'chile' => 'CL',
        'china' => 'CN', 'colombia' => 'CO', 'costa rica' => 'CR', 'croatia' => 'HR',
        'cuba' => 'CU', 'cyprus' => 'CY', 'czech republic' => 'CZ', 'czechia' => 'CZ',
        'denmark' => 'DK', 'dominican republic' => 'DO', 'ecuador' => 'EC', 'egypt' => 'EG',
        'el salvador' => 'SV', 'estonia' => 'EE', 'ethiopia' => 'ET', 'fiji' => 'FJ',
        'finland' => 'FI', 'france' => 'FR', 'germany' => 'DE', 'ghana' => 'GH',
        'greece' => 'GR', 'guatemala' => 'GT', 'haiti' => 'HT', 'honduras' => 'HN',
        'hong kong' => 'HK', 'hungary' => 'HU', 'iceland' => 'IS', 'india' => 'IN',
        'indonesia' => 'ID', 'iran' => 'IR', 'iraq' => 'IQ', 'ireland' => 'IE',
        'israel' => 'IL', 'italy' => 'IT', 'jamaica' => 'JM', 'japan' => 'JP',
        'jordan' => 'JO', 'kenya' => 'KE', 'kuwait' => 'KW', 'latvia' => 'LV',
        'lebanon' => 'LB', 'lithuania' => 'LT', 'luxembourg' => 'LU', 'malaysia' => 'MY',
        'maldives' => 'MV', 'mexico' => 'MX', 'morocco' => 'MA', 'myanmar' => 'MM',
        'nepal' => 'NP', 'netherlands' => 'NL', 'new zealand' => 'NZ', 'nicaragua' => 'NI',
        'nigeria' => 'NG', 'norway' => 'NO', 'oman' => 'OM', 'pakistan' => 'PK',
        'panama' => 'PA', 'paraguay' => 'PY', 'peru' => 'PE', 'philippines' => 'PH',
        'poland' => 'PL', 'portugal' => 'PT', 'qatar' => 'QA', 'romania' => 'RO',
        'russia' => 'RU', 'saudi arabia' => 'SA', 'senegal' => 'SN', 'serbia' => 'RS',
        'singapore' => 'SG', 'slovakia' => 'SK', 'slovenia' => 'SI', 'south africa' => 'ZA',
        'south korea' => 'KR', 'spain' => 'ES', 'sri lanka' => 'LK', 'sweden' => 'SE',
        'switzerland' => 'CH', 'taiwan' => 'TW', 'tanzania' => 'TZ', 'thailand' => 'TH',
        'trinidad and tobago' => 'TT', 'tunisia' => 'TN', 'turkey' => 'TR', 'turkiye' => 'TR',
        'ukraine' => 'UA', 'united arab emirates' => 'AE', 'united kingdom' => 'GB',
        'united states' => 'US', 'uruguay' => 'UY', 'venezuela' => 'VE', 'vietnam' => 'VN',
        // Common short forms
        'uk' => 'GB', 'usa' => 'US', 'uae' => 'AE',
    ];

    /**
     * Convert a country name or code to the ISO 3166-1 alpha-2 code.
     * Returns null if the input cannot be resolved.
     */
    public static function toCode(?string $country): ?string
    {
        if ($country === null || trim($country) === '') {
            return null;
        }

        $trimmed = trim($country);

        $key = strtolower($trimmed);

        // Check name/alias map first (handles 'uk', 'usa', 'uae', and full names)
        if (isset(self::MAP[$key])) {
            return self::MAP[$key];
        }

        // Already a valid 2-letter ISO code
        if (strlen($trimmed) === 2) {
            $upper = strtoupper($trimmed);
            if (in_array($upper, array_values(self::MAP), true)) {
                return $upper;
            }
        }

        return null;
    }

    /**
     * Check if a value is a valid ISO 3166-1 alpha-2 country code.
     */
    public static function isValid(?string $code): bool
    {
        if ($code === null || strlen(trim($code)) !== 2) {
            return false;
        }

        return in_array(strtoupper(trim($code)), array_values(self::MAP), true);
    }
}
