<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CancelPickupRequest;
use Johnpaulmedina\Usps\Http\Requests\SchedulePickupRequest;
use Johnpaulmedina\Usps\Http\Requests\UpdatePickupRequest;

class CarrierPickupController extends Controller
{
    public function store(SchedulePickupRequest $request): JsonResponse
    {
        $result = Usps::carrierPickup()->schedulePickup($request->validated());

        return response()->json($result, 201);
    }

    public function show(string $confirmationNumber): JsonResponse
    {
        $result = Usps::carrierPickup()->getPickup($confirmationNumber);

        return response()->json($result);
    }

    public function update(UpdatePickupRequest $request, string $confirmationNumber): JsonResponse
    {
        $validated = $request->validated();
        $etag = $validated['etag'];
        unset($validated['etag']);

        $result = Usps::carrierPickup()->updatePickup($confirmationNumber, $validated, $etag);

        return response()->json($result);
    }

    public function destroy(CancelPickupRequest $request, string $confirmationNumber): JsonResponse
    {
        $result = Usps::carrierPickup()->cancelPickup($confirmationNumber, $request->validated()['etag']);

        return response()->json($result);
    }
}
