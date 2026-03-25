<?php

/**
 * USPS Containers API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Containers extends USPSBase
{
    protected string $scope = 'containers';

    /**
     * Create a container label and associate packages.
     *
     * @param array<string, mixed> $containerData
     * @return array<string, mixed>
     */
    public function createContainer(array $containerData): array
    {
        return $this->apiPost('/containers/v3/containers', $containerData);
    }

    /**
     * Add packages to an existing container manifest.
     *
     * @param string $containerId
     * @param array{trackingNumbers?: string[], allowManifestedLabels?: bool} $data
     * @return array<string, mixed>
     */
    public function addPackages(string $containerId, array $data): array
    {
        return $this->apiPost("/containers/v3/containers/{$containerId}/packages", $data);
    }

    /**
     * Remove all packages from a container.
     *
     * @param string $containerId
     * @return array<string, mixed>
     */
    public function removeAllPackages(string $containerId): array
    {
        return $this->apiDelete("/containers/v3/containers/{$containerId}/packages");
    }

    /**
     * Remove a single package from a container.
     *
     * @param string $containerId
     * @param string $trackingNumber
     * @return array<string, mixed>
     */
    public function removePackage(string $containerId, string $trackingNumber): array
    {
        return $this->apiDelete("/containers/v3/containers/{$containerId}/packages/{$trackingNumber}");
    }

    /**
     * Close a container and generate a manifest.
     *
     * @param array{mailingDate?: string, containers: string[]} $manifestData
     * @return array<string, mixed>
     */
    public function createManifest(array $manifestData): array
    {
        return $this->apiPost('/containers/v3/containers/manifest', $manifestData);
    }
}
