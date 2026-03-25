<?php

/**
 * USPS SCAN Form API v3
 * POST /scan-forms/v3/scan-form
 *
 * Creates Shipment Confirmation Acceptance Notice (SCAN) forms to link
 * multiple labels through a single Electronic File Number (EFN).
 * Supports Label Shipment, MID Shipment, and Manifest MID Shipment types.
 *
 * @since  2.1
 * @author John Paul Medina
 * @see    https://github.com/USPS/api-examples
 */

namespace Johnpaulmedina\Usps;

class ScanForm extends USPSBase
{
    protected string $scope = 'scan-forms';

    /**
     * Create a SCAN form for label shipments.
     *
     * Links individual tracking numbers to a single EFN for USPS acceptance.
     *
     * @param array{
     *   trackingNumbers: array<string>,
     *   labelIDs?: array<string>,
     *   overrideAddress?: array{streetAddress: string, city: string, state: string, ZIPCode: string},
     * } $data
     * @return array<string, mixed>
     */
    public function createLabelShipment(array $data): array
    {
        $payload = array_merge(['scanFormType' => 'LABEL_SHIPMENT'], $data);

        return $this->apiPost('/scan-forms/v3/scan-form', $payload);
    }

    /**
     * Create a SCAN form for MID (Mailer ID) shipments.
     *
     * Links labels by Mailer ID to a single EFN.
     *
     * @param array{
     *   MID: string,
     *   trackingNumbers: array<string>,
     *   overrideAddress?: array{streetAddress: string, city: string, state: string, ZIPCode: string},
     * } $data
     * @return array<string, mixed>
     */
    public function createMidShipment(array $data): array
    {
        $payload = array_merge(['scanFormType' => 'MID_SHIPMENT'], $data);

        return $this->apiPost('/scan-forms/v3/scan-form', $payload);
    }

    /**
     * Create a SCAN form for Manifest MID shipments.
     *
     * Multi-manifest processing for high-volume shippers.
     *
     * @param array{
     *   manifestMID: string,
     *   MID: string,
     *   trackingNumbers?: array<string>,
     *   overrideAddress?: array{streetAddress: string, city: string, state: string, ZIPCode: string},
     * } $data
     * @return array<string, mixed>
     */
    public function createManifestMidShipment(array $data): array
    {
        $payload = array_merge(['scanFormType' => 'MANIFEST_MID_SHIPMENT'], $data);

        return $this->apiPost('/scan-forms/v3/scan-form', $payload);
    }
}
