<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\ProofOfDeliveryRequest;
use Johnpaulmedina\Usps\Http\Requests\TrackingNotificationRequest;
use Johnpaulmedina\Usps\Http\Requests\TrackPackageRequest;

class TrackingController extends Controller
{
    public function store(TrackPackageRequest $request): JsonResponse
    {
        $result = Usps::tracking()->track($request->validated());

        return response()->json($result);
    }

    public function notifications(TrackingNotificationRequest $request, string $trackingNumber): JsonResponse
    {
        $result = Usps::tracking()->registerNotifications($trackingNumber, $request->validated());

        return response()->json($result, 201);
    }

    public function proofOfDelivery(ProofOfDeliveryRequest $request, string $trackingNumber): JsonResponse
    {
        $result = Usps::tracking()->proofOfDelivery($trackingNumber, $request->validated());

        return response()->json($result);
    }
}
