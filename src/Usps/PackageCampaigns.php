<?php

/**
 * USPS Informed Delivery Package Campaigns API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class PackageCampaigns extends USPSBase
{
    protected string $scope = 'informed-delivery-package-campaigns';

    /**
     * Create an Informed Delivery package campaign.
     *
     * @param array<string, mixed> $campaignData
     * @return array<string, mixed>
     */
    public function createCampaign(array $campaignData): array
    {
        return $this->apiPost('/informed-delivery-package-campaigns/v3/package-campaigns', $campaignData);
    }

    /**
     * Search all package campaigns for a submitter CRID.
     *
     * @param array{CRID?: string, status?: string, startDate?: string, endDate?: string, offset?: int, limit?: int, sort?: string} $options
     * @return array<string, mixed>
     */
    public function searchCampaigns(array $options = []): array
    {
        return $this->apiGet('/informed-delivery-package-campaigns/v3/package-campaigns', $options);
    }

    /**
     * Return a single Informed Delivery package campaign.
     *
     * @param string $campaignId
     * @return array<string, mixed>
     */
    public function getCampaign(string $campaignId): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiGet("/informed-delivery-package-campaigns/v3/package-campaigns/{$encoded}");
    }

    /**
     * Edit an Informed Delivery package campaign.
     *
     * @param string $campaignId
     * @param array<string, mixed> $campaignData
     * @return array<string, mixed>
     */
    public function updateCampaign(string $campaignId, array $campaignData): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiPut("/informed-delivery-package-campaigns/v3/package-campaigns/{$encoded}", $campaignData);
    }

    /**
     * Cancel an Informed Delivery package campaign.
     *
     * @param string $campaignId
     * @return array<string, mixed>
     */
    public function cancelCampaign(string $campaignId): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiDelete("/informed-delivery-package-campaigns/v3/package-campaigns/{$encoded}");
    }

    /**
     * Add tracking numbers to a package campaign.
     *
     * @param string $campaignId
     * @param array<string, mixed> $trackingData
     * @return array<string, mixed>
     */
    public function addTrackingNumbers(string $campaignId, array $trackingData): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiPost("/informed-delivery-package-campaigns/v3/package-campaigns/{$encoded}/tracking-numbers", $trackingData);
    }
}
