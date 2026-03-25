<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class ServiceStandardsCommand extends Command
{
    protected $signature = 'usps:standards
        {origin : Origin ZIP code}
        {destination : Destination ZIP code}
        {--mail-class= : Filter by mail class}';

    protected $description = 'Get USPS delivery estimates between two ZIP codes';

    public function handle(): int
    {
        $usps = app('usps');
        $options = [];
        if ($this->option('mail-class')) {
            $options['mailClass'] = $this->option('mail-class');
        }

        $result = $usps->serviceStandards()->getEstimates(
            $this->argument('origin'),
            $this->argument('destination'),
            $options
        );

        if (isset($result['error'])) {
            $this->error($result['error']['message'] ?? 'Standards lookup failed.');
            return self::FAILURE;
        }

        $estimates = $result['estimates'] ?? $result['deliveryEstimates'] ?? [$result];
        $rows = [];
        foreach ($estimates as $est) {
            $rows[] = [
                $est['mailClass'] ?? '',
                $est['deliveryDate'] ?? '',
                $est['deliveryDays'] ?? '',
                $est['acceptanceDate'] ?? '',
            ];
        }

        $this->table(['Mail Class', 'Delivery Date', 'Days', 'Acceptance'], $rows);

        return self::SUCCESS;
    }
}
