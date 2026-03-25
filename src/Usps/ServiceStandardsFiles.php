<?php

/**
 * USPS Service Standards File Download API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class ServiceStandardsFiles extends USPSBase
{
    protected string $scope = 'service-standards-files';

    /**
     * List available files for download.
     *
     * @return array<string, mixed>
     */
    public function listFiles(): array
    {
        return $this->apiGet('/service-standards-files/v3/files');
    }

    /**
     * Generate a signed URL for downloading a file.
     *
     * @param string $fileId The file identifier
     * @return array<string, mixed>
     */
    public function generateSignedUrl(string $fileId): array
    {
        return $this->apiGet("/service-standards-files/v3/files/{$fileId}/generate-signed-url");
    }
}
