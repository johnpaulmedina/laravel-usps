<?php

/**
 * USPS International Prices API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

use Johnpaulmedina\Usps\Validation\ValidatesNumeric;

class InternationalPrices extends USPSBase
{
    use ValidatesNumeric;

    protected string $scope = 'international-prices';

    /**
     * Search for international base postage rates using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if weight is present and invalid
     */
    public function baseRateSearch(array $rateIngredients): array
    {
        $this->validateWeightIfPresent($rateIngredients);
        return $this->apiPost('/international-prices/v3/base-rates/search', $rateIngredients);
    }

    /**
     * Search for international extra service rates using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function extraServiceRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/international-prices/v3/extra-service-rates/search', $rateIngredients);
    }

    /**
     * Search for eligible international products using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if weight is present and invalid
     */
    public function baseRateListSearch(array $rateIngredients): array
    {
        $this->validateWeightIfPresent($rateIngredients);
        return $this->apiPost('/international-prices/v3/base-rates-list/search', $rateIngredients);
    }

    /**
     * Return total international rates including extra service fees.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if weight is present and invalid
     */
    public function totalRateSearch(array $rateIngredients): array
    {
        $this->validateWeightIfPresent($rateIngredients);
        return $this->apiPost('/international-prices/v3/total-rates/search', $rateIngredients);
    }

    /**
     * Search for First-Class Mail International letter prices.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException if weight is present and invalid
     */
    public function letterRateSearch(array $rateIngredients): array
    {
        $this->validateWeightIfPresent($rateIngredients);
        return $this->apiPost('/international-prices/v3/letter-rates/search', $rateIngredients);
    }

    /**
     * Validate the weight field if present in rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     */
    private function validateWeightIfPresent(array $rateIngredients): void
    {
        if (isset($rateIngredients['weight'])) {
            $this->validatePositiveFloat($rateIngredients['weight'], 'weight');
        }
    }
}
