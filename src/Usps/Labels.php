<?php

/**
 * USPS Domestic Labels API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Labels extends USPSBase
{
    protected string $scope = 'labels';

    /**
     * Create a domestic shipping label.
     *
     * @param array<string, mixed> $labelData
     * @param string $paymentToken Payment authorization token from Payments API
     * @param string|null $idempotencyKey Optional idempotency key
     * @return array<string, mixed>
     */
    public function createLabel(array $labelData, string $paymentToken, ?string $idempotencyKey = null): array
    {
        $headers = ['X-Payment-Authorization-Token' => $paymentToken];
        if ($idempotencyKey !== null) {
            $headers['X-Idempotency-Key'] = $idempotencyKey;
        }

        return $this->apiPost('/labels/v3/label', $labelData, $headers);
    }

    /**
     * Create a domestic returns shipping label.
     *
     * @param array<string, mixed> $labelData
     * @param string $paymentToken Payment authorization token
     * @param string|null $idempotencyKey Optional idempotency key
     * @return array<string, mixed>
     */
    public function createReturnLabel(array $labelData, string $paymentToken, ?string $idempotencyKey = null): array
    {
        $headers = ['X-Payment-Authorization-Token' => $paymentToken];
        if ($idempotencyKey !== null) {
            $headers['X-Idempotency-Key'] = $idempotencyKey;
        }

        return $this->apiPost('/labels/v3/return-label', $labelData, $headers);
    }

    /**
     * Cancel a previously requested label or request a refund.
     *
     * @param string $trackingNumber
     * @return array<string, mixed>
     */
    public function cancelLabel(string $trackingNumber): array
    {
        return $this->apiDelete("/labels/v3/label/{$trackingNumber}");
    }

    /**
     * Edit label attributes (PATCH).
     *
     * @param string $trackingNumber
     * @param array<string, mixed> $patchData
     * @return array<string, mixed>
     */
    public function editLabel(string $trackingNumber, array $patchData): array
    {
        return $this->apiPatch("/labels/v3/label/{$trackingNumber}", $patchData);
    }

    /**
     * Create a First-Class letter, flat, or card indicia.
     *
     * @param array<string, mixed> $indiciaData
     * @param string $paymentToken Payment authorization token
     * @return array<string, mixed>
     */
    public function createIndicia(array $indiciaData, string $paymentToken): array
    {
        return $this->apiPost('/labels/v3/indicia', $indiciaData, [
            'X-Payment-Authorization-Token' => $paymentToken,
        ]);
    }

    /**
     * Create an Intelligent Mail Barcode (IMB) label.
     *
     * @param array<string, mixed> $imbData
     * @param string $paymentToken Payment authorization token
     * @return array<string, mixed>
     */
    public function createImb(array $imbData, string $paymentToken): array
    {
        return $this->apiPost('/labels/v3/indicia/imb', $imbData, [
            'X-Payment-Authorization-Token' => $paymentToken,
        ]);
    }

    /**
     * Cancel a previously requested IMB label.
     *
     * @param string $imb The Intelligent Mail Barcode
     * @return array<string, mixed>
     */
    public function cancelImb(string $imb): array
    {
        return $this->apiDelete("/labels/v3/indicia/imb/{$imb}");
    }

    /**
     * Upload an SVG image for label branding.
     *
     * @param array<string, mixed> $brandingData
     * @return array<string, mixed>
     */
    public function uploadBranding(array $brandingData): array
    {
        return $this->apiPost('/labels/v3/branding', $brandingData);
    }

    /**
     * Retrieve a paginated list of label branding images.
     *
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $orderBy
     * @param string|null $sortBy
     * @return array<string, mixed>
     */
    public function listBranding(?int $limit = null, ?int $offset = null, ?string $orderBy = null, ?string $sortBy = null): array
    {
        return $this->apiGet('/labels/v3/branding', array_filter([
            'limit' => $limit,
            'offset' => $offset,
            'orderBy' => $orderBy,
            'sortBy' => $sortBy,
        ], fn ($v) => $v !== null));
    }

    /**
     * Retrieve a single label branding image.
     *
     * @param string $imageUUID
     * @return array<string, mixed>
     */
    public function getBranding(string $imageUUID): array
    {
        return $this->apiGet("/labels/v3/branding/{$imageUUID}");
    }

    /**
     * Delete a label branding image.
     *
     * @param string $imageUUID
     * @return array<string, mixed>
     */
    public function deleteBranding(string $imageUUID): array
    {
        return $this->apiDelete("/labels/v3/branding/{$imageUUID}");
    }

    /**
     * Rename a label branding image.
     *
     * @param string $imageUUID
     * @param array<string, mixed> $patchData
     * @return array<string, mixed>
     */
    public function renameBranding(string $imageUUID, array $patchData): array
    {
        return $this->apiPatch("/labels/v3/branding/{$imageUUID}", $patchData);
    }

    /**
     * Reprint a domestic shipping label.
     *
     * @param string $trackingNumber
     * @param array<string, mixed> $reprintData
     * @param string $paymentToken Payment authorization token
     * @return array<string, mixed>
     */
    public function reprintLabel(string $trackingNumber, array $reprintData, string $paymentToken): array
    {
        return $this->apiPost("/labels/v3/label-reprint/{$trackingNumber}", $reprintData, [
            'X-Payment-Authorization-Token' => $paymentToken,
        ]);
    }
}
