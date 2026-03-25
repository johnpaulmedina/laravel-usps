<?php

/**
 * USPS International Service Standards API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class InternationalServiceStandards extends USPSBase
{
    protected string $scope = 'international-service-standard';

    /**
     * Get international service standard message for a country and mail class.
     *
     * @param string $countryCode ISO 2-character country code
     * @param string $mailClass One of: FIRST-CLASS_PACKAGE_INTERNATIONAL_SERVICE, PRIORITY_MAIL_INTERNATIONAL, PRIORITY_MAIL_EXPRESS_INTERNATIONAL
     * @return array<string, mixed>
     */
    public function getServiceStandard(string $countryCode, string $mailClass): array
    {
        return $this->apiGet('/international-service-standard/v3/international-service-standard', [
            'countryCode' => $countryCode,
            'mailClass' => $mailClass,
        ]);
    }
}
