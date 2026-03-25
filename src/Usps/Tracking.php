<?php

/**
 * USPS Package Tracking API v3r2
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Tracking extends USPSBase
{
    protected string $scope = 'tracking';

    /**
     * Get package tracking status and delivery information.
     *
     * @param array<int, array{trackingNumber: string, mailingDate?: string, destinationZIPCode?: string}> $trackingRequests
     * @return array<string, mixed>
     */
    public function track(array $trackingRequests): array
    {
        return $this->apiPost('/tracking/v3r2/tracking', $trackingRequests);
    }

    /**
     * Register for tracking notifications for a given tracking number.
     *
     * @param string $trackingNumber
     * @param array{uniqueTrackingID: string, mailingDate?: string, notifyEventTypes: string[], recipients: array<int, array{email: string, firstName?: string, lastName?: string}>} $notificationRequest
     * @return array<string, mixed>
     */
    public function registerNotifications(string $trackingNumber, array $notificationRequest): array
    {
        return $this->apiPost(
            "/tracking/v3r2/tracking/{$trackingNumber}/notifications",
            $notificationRequest
        );
    }

    /**
     * Request proof of delivery for a given tracking number.
     *
     * @param string $trackingNumber
     * @param array{uniqueTrackingID: string, mailingDate?: string, letterType?: string, recipients: array<int, array{email: string, firstName: string, lastName: string}>, CRID?: string} $request
     * @return array<string, mixed>
     */
    public function proofOfDelivery(string $trackingNumber, array $request): array
    {
        return $this->apiPost(
            "/tracking/v3r2/tracking/{$trackingNumber}/proof-of-delivery",
            $request
        );
    }
}
