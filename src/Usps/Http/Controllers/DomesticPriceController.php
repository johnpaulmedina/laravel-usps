<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\BaseRateSearchRequest;
use Johnpaulmedina\Usps\Http\Requests\ExtraServiceRateSearchRequest;
use Johnpaulmedina\Usps\Http\Requests\TotalRateSearchRequest;

class DomesticPriceController extends Controller
{
    public function baseRates(BaseRateSearchRequest $request): JsonResponse
    {
        $result = Usps::domesticPrices()->baseRateSearch($request->validated());

        return response()->json($result);
    }

    public function extraServices(ExtraServiceRateSearchRequest $request): JsonResponse
    {
        $result = Usps::domesticPrices()->extraServiceRateSearch($request->validated());

        return response()->json($result);
    }

    public function totalRates(TotalRateSearchRequest $request): JsonResponse
    {
        $result = Usps::domesticPrices()->totalRateSearch($request->validated());

        return response()->json($result);
    }
}
