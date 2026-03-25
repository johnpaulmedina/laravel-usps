<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\DropoffLocationsRequest;
use Johnpaulmedina\Usps\Http\Requests\ParcelLockerLocationsRequest;
use Johnpaulmedina\Usps\Http\Requests\PostOfficeLocationsRequest;

class LocationController extends Controller
{
    public function postOffices(PostOfficeLocationsRequest $request): JsonResponse
    {
        $result = Usps::locations()->getPostOfficeLocations($request->validated());

        return response()->json($result);
    }

    public function dropoff(DropoffLocationsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $destinationZIPCode = $validated['destinationZIPCode'];
        unset($validated['destinationZIPCode']);

        $result = Usps::locations()->getDropoffLocations($destinationZIPCode, $validated);

        return response()->json($result);
    }

    public function parcelLockers(ParcelLockerLocationsRequest $request): JsonResponse
    {
        $result = Usps::locations()->getParcelLockerLocations($request->validated());

        return response()->json($result);
    }
}
