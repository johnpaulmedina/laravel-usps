<?php

namespace Johnpaulmedina\Usps\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Johnpaulmedina\Usps\Appointments;
use Orchestra\Testbench\TestCase;

class AppointmentsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [\Johnpaulmedina\Usps\UspsServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::put('usps_oauth_token_' . md5('test-id_fast-appointments'), 'fake-token', 3600);
    }

    private function api(): Appointments
    {
        return new Appointments('test-id', 'test-secret');
    }

    public function test_create_appointment(): void
    {
        Http::fake([
            'apis.usps.com/fast-appointments/v3/fast-appointments/appointment' => Http::response([
                'appointmentId' => 'APT-001',
                'status' => 'CONFIRMED',
            ], 201),
        ]);

        $result = $this->api()->createAppointment(['facilityId' => 'FAC-001', 'date' => '2026-04-01']);
        $this->assertEquals('APT-001', $result['appointmentId']);
        Http::assertSent(fn ($r) => $r->method() === 'POST');
    }

    public function test_update_appointment(): void
    {
        Http::fake([
            'apis.usps.com/fast-appointments/v3/fast-appointments/appointment' => Http::response([
                'appointmentId' => 'APT-001',
                'status' => 'CONFIRMED',
            ]),
        ]);

        $result = $this->api()->updateAppointment(['appointmentId' => 'APT-001', 'date' => '2026-04-02']);
        $this->assertArrayHasKey('appointmentId', $result);
        Http::assertSent(fn ($r) => $r->method() === 'PUT');
    }

    public function test_cancel_appointment(): void
    {
        Http::fake([
            'apis.usps.com/fast-appointments/v3/fast-appointments/appointment' => Http::response([]),
        ]);

        $this->api()->cancelAppointment(['appointmentId' => 'APT-001']);
        Http::assertSent(fn ($r) => $r->method() === 'DELETE');
    }

    public function test_get_availability(): void
    {
        Http::fake([
            'apis.usps.com/fast-appointments/v3/appointment-availability*' => Http::response([
                'slots' => [['date' => '2026-04-01', 'time' => '0800']],
            ]),
        ]);

        $result = $this->api()->getAvailability(['facilityId' => 'FAC-001', 'date' => '2026-04-01']);
        $this->assertArrayHasKey('slots', $result);
    }
}
