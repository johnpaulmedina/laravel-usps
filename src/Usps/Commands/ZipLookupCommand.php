<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class ZipLookupCommand extends Command
{
    protected $signature = 'usps:zip
        {zip : 5-digit ZIP code to look up city/state}';

    protected $description = 'Look up city and state for a ZIP code';

    public function handle(): int
    {
        $usps = app('usps');
        $zip = $this->argument('zip');
        $result = $usps->cityStateLookup($zip);

        if (isset($result['error'])) {
            $this->error($result['error']['message'] ?? 'Lookup failed.');
            return self::FAILURE;
        }

        $this->info('ZIP: ' . ($result['ZIPCode'] ?? $zip));
        $this->info('City: ' . ($result['city'] ?? 'Unknown'));
        $this->info('State: ' . ($result['state'] ?? 'Unknown'));

        return self::SUCCESS;
    }
}
