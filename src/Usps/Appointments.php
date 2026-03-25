<?php

/**
 * USPS Appointments API v3 (FAST Appointments)
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class Appointments extends USPSBase
{
    protected string $scope = 'fast-appointments';

    /**
     * Create an appointment at a USPS facility.
     *
     * @param array<string, mixed> $appointmentData
     * @return array<string, mixed>
     */
    public function createAppointment(array $appointmentData): array
    {
        return $this->apiPost('/fast-appointments/v3/fast-appointments/appointment', $appointmentData);
    }

    /**
     * Update an existing appointment.
     *
     * @param array<string, mixed> $appointmentData
     * @return array<string, mixed>
     */
    public function updateAppointment(array $appointmentData): array
    {
        return $this->apiPut('/fast-appointments/v3/fast-appointments/appointment', $appointmentData);
    }

    /**
     * Cancel an appointment.
     *
     * @param array<string, mixed> $cancellationData
     * @return array<string, mixed>
     */
    public function cancelAppointment(array $cancellationData): array
    {
        return $this->apiDelete('/fast-appointments/v3/fast-appointments/appointment', $cancellationData);
    }

    /**
     * Request available appointment times at a facility.
     *
     * @param array{facilityId?: string, date?: string, mailClass?: string} $options
     * @return array<string, mixed>
     */
    public function getAvailability(array $options = []): array
    {
        return $this->apiGet('/fast-appointments/v3/appointment-availability', $options);
    }
}
