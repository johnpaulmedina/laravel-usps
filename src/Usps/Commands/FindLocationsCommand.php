<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class FindLocationsCommand extends Command
{
    protected $signature = 'usps:locations
        {zip : ZIP code to search near}
        {--type=post-office : Location type: post-office, dropoff, parcel-locker}
        {--radius=10 : Search radius in miles}';

    protected $description = 'Find USPS locations near a ZIP code';

    public function handle(): int
    {
        $usps = app('usps');
        $zip = $this->argument('zip');
        $type = $this->option('type');
        $params = ['ZIPCode' => $zip, 'radius' => (int) $this->option('radius')];

        $result = match ($type) {
            'dropoff' => $usps->locations()->getDropoffLocations($zip, $params),
            'parcel-locker' => $usps->locations()->getParcelLockerLocations($params),
            default => $usps->locations()->getPostOfficeLocations($params),
        };

        if (isset($result['error'])) {
            $this->error($result['error']['message'] ?? 'Location lookup failed.');
            return self::FAILURE;
        }

        $locations = $result['locations'] ?? $result['postOffices'] ?? $result['lockerLocations'] ?? [$result];
        $rows = [];
        foreach (array_slice($locations, 0, 15) as $loc) {
            $rows[] = [
                $loc['facilityName'] ?? $loc['locationName'] ?? '',
                $loc['streetAddress'] ?? $loc['address'] ?? '',
                $loc['city'] ?? '',
                $loc['state'] ?? '',
                $loc['ZIPCode'] ?? $loc['zip5'] ?? '',
                ($loc['distance'] ?? '') . ' mi',
            ];
        }

        $this->table(['Name', 'Address', 'City', 'State', 'ZIP', 'Distance'], $rows);

        return self::SUCCESS;
    }
}
