<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\ServiceEstimatesRequest;
use Johnpaulmedina\Usps\Http\Requests\ServiceStandardsRequest;

class ServiceStandardController extends Controller
{
    public function estimates(ServiceEstimatesRequest $request): JsonResponse
    {
        $result = Usps::serviceStandards()->getEstimates(
            $request->validated()['originZIPCode'],
            $request->validated()['destinationZIPCode'],
            collect($request->validated())->except(['originZIPCode', 'destinationZIPCode'])->toArray()
        );

        return response()->json($result);
    }

    public function standards(ServiceStandardsRequest $request): JsonResponse
    {
        $result = Usps::serviceStandards()->getStandards(
            $request->validated()['originZIPCode'],
            $request->validated()['destinationZIPCode'],
            collect($request->validated())->except(['originZIPCode', 'destinationZIPCode'])->toArray()
        );

        return response()->json($result);
    }
}
