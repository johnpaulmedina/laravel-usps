<?php

/**
 * USPS International Prices API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class InternationalPrices extends USPSBase
{
    protected string $scope = 'international-prices';

    /**
     * Search for international base postage rates using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function baseRateSearch(array $rateIngredients): array
    {
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
     */
    public function baseRateListSearch(array $rateIngredients): array
    {
        return $this->apiPost('/international-prices/v3/base-rates-list/search', $rateIngredients);
    }

    /**
     * Return total international rates including extra service fees.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function totalRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/international-prices/v3/total-rates/search', $rateIngredients);
    }

    /**
     * Search for First-Class Mail International letter prices.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function letterRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/international-prices/v3/letter-rates/search', $rateIngredients);
    }
}
