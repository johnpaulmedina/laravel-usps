<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class TrackPackageCommand extends Command
{
    protected $signature = 'usps:track
        {tracking* : One or more tracking numbers}';

    protected $description = 'Track one or more USPS packages';

    public function handle(): int
    {
        $usps = app('usps');
        $numbers = $this->argument('tracking');
        $packages = array_map(fn (string $tn) => ['trackingNumber' => $tn], $numbers);

        $result = $usps->tracking()->track($packages);

        if (isset($result['error'])) {
            $this->error($result['error']['message'] ?? 'Tracking failed.');
            return self::FAILURE;
        }

        $trackingResults = $result['trackingResults'] ?? $result['trackResults'] ?? [$result];

        foreach ($trackingResults as $pkg) {
            $tn = $pkg['trackingNumber'] ?? $numbers[0] ?? 'Unknown';
            $this->info("Tracking: {$tn}");

            $events = $pkg['trackingEvents'] ?? $pkg['trackingEventList'] ?? [];
            if (empty($events)) {
                $status = $pkg['statusCategory'] ?? $pkg['status'] ?? 'No events';
                $this->line("  Status: {$status}");
                continue;
            }

            $rows = [];
            foreach (array_slice($events, 0, 10) as $event) {
                $rows[] = [
                    $event['eventDate'] ?? $event['date'] ?? '',
                    $event['eventTime'] ?? $event['time'] ?? '',
                    $event['event'] ?? $event['eventDescription'] ?? '',
                    trim(($event['eventCity'] ?? '') . ', ' . ($event['eventState'] ?? '') . ' ' . ($event['eventZIPCode'] ?? ''), ', '),
                ];
            }
            $this->table(['Date', 'Time', 'Event', 'Location'], $rows);
        }

        return self::SUCCESS;
    }
}
