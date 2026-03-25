<?php

/**
 * USPS Service Standards Directory API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class ServiceStandardsDirectory extends USPSBase
{
    protected string $scope = 'service-standards-directory';

    /**
     * Return valid 5-digit ZIP codes that have service standards.
     *
     * @return array<string, mixed>
     */
    public function getValidZip5Codes(): array
    {
        return $this->apiGet('/service-standards-directory/v3/zip5Codes');
    }

    /**
     * Return directory of service standards.
     *
     * @param array{originZIPCode?: string, destinationZIPCode?: string, responseFormat?: string, mailClass?: string, offset?: int, limit?: int, returnUnsupportedZIPCodes?: bool} $request
     * @return array<string, mixed>
     */
    public function getReport(array $request): array
    {
        return $this->apiPost('/service-standards-directory/v3/reporter', $request);
    }
}
