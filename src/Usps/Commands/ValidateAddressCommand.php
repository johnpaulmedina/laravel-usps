<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class ValidateAddressCommand extends Command
{
    protected $signature = 'usps:validate
        {street : Street address}
        {--city= : City name}
        {--state= : Two-letter state code}
        {--zip= : 5-digit ZIP code}
        {--apt= : Apartment/suite number}';

    protected $description = 'Validate and standardize a USPS address';

    public function handle(): int
    {
        $usps = app('usps');
        $result = $usps->validate([
            'Address' => $this->argument('street'),
            'City' => $this->option('city') ?? '',
            'State' => $this->option('state') ?? '',
            'Zip' => $this->option('zip') ?? '',
            'Apartment' => $this->option('apt') ?? '',
        ]);

        if (isset($result['error'])) {
            $this->error($result['error']);
            return self::FAILURE;
        }

        $addr = $result['address'] ?? [];
        $this->info('Validated Address:');
        $this->table(
            ['Field', 'Value'],
            collect($addr)->map(fn ($v, $k) => [$k, $v ?? ''])->values()->toArray()
        );

        return self::SUCCESS;
    }
}
