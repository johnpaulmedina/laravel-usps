<?php

namespace Johnpaulmedina\Usps\Commands;

use Illuminate\Console\Command;


class PriceCalculatorCommand extends Command
{
    protected $signature = 'usps:price
        {origin : Origin ZIP code}
        {destination : Destination ZIP code}
        {weight : Weight in ounces}
        {--mail-class=USPS_GROUND_ADVANTAGE : Mail class}
        {--length=0 : Length in inches}
        {--width=0 : Width in inches}
        {--height=0 : Height in inches}';

    protected $description = 'Calculate USPS shipping rates';

    public function handle(): int
    {
        $usps = app('usps');
        $result = $usps->domesticPrices()->baseRateSearch([
            'originZIPCode' => $this->argument('origin'),
            'destinationZIPCode' => $this->argument('destination'),
            'weight' => (float) $this->argument('weight'),
            'mailClass' => $this->option('mail-class'),
            'length' => (float) $this->option('length'),
            'width' => (float) $this->option('width'),
            'height' => (float) $this->option('height'),
        ]);

        if (isset($result['error'])) {
            $this->error($result['error']['message'] ?? 'Price lookup failed.');
            return self::FAILURE;
        }

        $rates = $result['rates'] ?? [$result];
        $rows = [];
        foreach ($rates as $rate) {
            $rows[] = [
                $rate['mailClass'] ?? $rate['productName'] ?? '',
                '$' . number_format((float) ($rate['totalBasePrice'] ?? $rate['price'] ?? 0), 2),
                $rate['deliveryDays'] ?? $rate['commitmentName'] ?? '',
            ];
        }

        $this->table(['Mail Class', 'Price', 'Delivery'], $rows);

        return self::SUCCESS;
    }
}
