<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\InternationalBaseRateSearchRequest;
use Johnpaulmedina\Usps\Http\Requests\InternationalTotalRateSearchRequest;

class InternationalPriceController extends Controller
{
    public function baseRates(InternationalBaseRateSearchRequest $request): JsonResponse
    {
        $result = Usps::internationalPrices()->baseRateSearch($request->validated());

        return response()->json($result);
    }

    public function totalRates(InternationalTotalRateSearchRequest $request): JsonResponse
    {
        $result = Usps::internationalPrices()->totalRateSearch($request->validated());

        return response()->json($result);
    }
}
