<?php

/**
 * USPS Domestic Prices API v3
 *
 * @since  2.0
 * @author John Paul Medina
 */

namespace Johnpaulmedina\Usps;

class DomesticPrices extends USPSBase
{
    protected string $scope = 'prices';

    /**
     * Search for base postage rates using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function baseRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/prices/v3/base-rates/search', $rateIngredients);
    }

    /**
     * Search for extra service rates using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function extraServiceRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/prices/v3/extra-service-rates/search', $rateIngredients);
    }

    /**
     * Search for eligible products (base rates list) using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function baseRateListSearch(array $rateIngredients): array
    {
        return $this->apiPost('/prices/v3/base-rates-list/search', $rateIngredients);
    }

    /**
     * Return total rates including extra service fees for a set of package rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function totalRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/prices/v3/total-rates/search', $rateIngredients);
    }

    /**
     * Search for First-Class Mail letter prices using rate ingredients.
     *
     * @param array<string, mixed> $rateIngredients
     * @return array<string, mixed>
     */
    public function letterRateSearch(array $rateIngredients): array
    {
        return $this->apiPost('/prices/v3/letter-rates/search', $rateIngredients);
    }
}
