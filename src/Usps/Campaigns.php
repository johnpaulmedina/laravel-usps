<?php

/**
 * USPS Informed Delivery Mail Campaigns API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Campaigns extends USPSBase
{
    protected string $scope = 'informed-delivery-campaigns';

    /**
     * Create an Informed Delivery mail campaign.
     *
     * @param array<string, mixed> $campaignData
     * @return array<string, mixed>
     */
    public function createCampaign(array $campaignData): array
    {
        return $this->apiPost('/informed-delivery-campaigns/v3/campaigns', $campaignData);
    }

    /**
     * Search all campaigns for a submitter CRID.
     *
     * @param array{CRID?: string, status?: string, startDate?: string, endDate?: string, offset?: int, limit?: int, sort?: string} $options
     * @return array<string, mixed>
     */
    public function searchCampaigns(array $options = []): array
    {
        return $this->apiGet('/informed-delivery-campaigns/v3/campaigns', $options);
    }

    /**
     * Return a single Informed Delivery campaign.
     *
     * @param string $campaignId
     * @return array<string, mixed>
     */
    public function getCampaign(string $campaignId): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiGet("/informed-delivery-campaigns/v3/campaigns/{$encoded}");
    }

    /**
     * Cancel an Informed Delivery campaign.
     *
     * @param string $campaignId
     * @return array<string, mixed>
     */
    public function cancelCampaign(string $campaignId): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiDelete("/informed-delivery-campaigns/v3/campaigns/{$encoded}");
    }

    /**
     * Edit an Informed Delivery campaign.
     *
     * @param string $campaignId
     * @param array<string, mixed> $campaignData
     * @return array<string, mixed>
     */
    public function updateCampaign(string $campaignId, array $campaignData): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiPut("/informed-delivery-campaigns/v3/campaigns/{$encoded}", $campaignData);
    }

    /**
     * Add IMBs to a NON-SEQ campaign.
     *
     * @param string $campaignId
     * @param array<string, mixed> $imbData
     * @return array<string, mixed>
     */
    public function addImbs(string $campaignId, array $imbData): array
    {
        $encoded = rawurlencode($campaignId);
        return $this->apiPost("/informed-delivery-campaigns/v3/campaigns/{$encoded}/imbs", $imbData);
    }

    /**
     * Search all callback keys for a submitter CRID.
     *
     * @param string $crid
     * @param array{offset?: int, limit?: int} $options
     * @return array<string, mixed>
     */
    public function getCallbackKeys(string $crid, array $options = []): array
    {
        $encoded = rawurlencode($crid);
        return $this->apiGet("/informed-delivery-campaigns/v3/campaigns/{$encoded}/callbacks", $options);
    }

    /**
     * Return summary-level info on a single callback key transaction.
     *
     * @param string $crid
     * @param string $callbackKey
     * @return array<string, mixed>
     */
    public function getCallbackSummary(string $crid, string $callbackKey): array
    {
        $encodedCrid = rawurlencode($crid);
        $encodedKey = rawurlencode($callbackKey);
        return $this->apiGet("/informed-delivery-campaigns/v3/campaigns/{$encodedCrid}/{$encodedKey}/summary");
    }

    /**
     * Return IMB-level errors for a single callback key transaction.
     *
     * @param string $crid
     * @param string $callbackKey
     * @return array<string, mixed>
     */
    public function getCallbackDetails(string $crid, string $callbackKey): array
    {
        $encodedCrid = rawurlencode($crid);
        $encodedKey = rawurlencode($callbackKey);
        return $this->apiGet("/informed-delivery-campaigns/v3/campaigns/{$encodedCrid}/{$encodedKey}/details");
    }
}
