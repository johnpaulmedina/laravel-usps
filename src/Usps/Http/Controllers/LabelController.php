<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CreateLabelRequest;
use Johnpaulmedina\Usps\Http\Requests\CreateReturnLabelRequest;

class LabelController extends Controller
{
    public function store(CreateLabelRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $paymentToken = $validated['paymentToken'];
        $idempotencyKey = $validated['idempotencyKey'] ?? null;

        unset($validated['paymentToken'], $validated['idempotencyKey']);

        $result = Usps::labels()->createLabel($validated, $paymentToken, $idempotencyKey);

        return response()->json($result, 201);
    }

    public function storeReturn(CreateReturnLabelRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $paymentToken = $validated['paymentToken'];
        $idempotencyKey = $validated['idempotencyKey'] ?? null;

        unset($validated['paymentToken'], $validated['idempotencyKey']);

        $result = Usps::labels()->createReturnLabel($validated, $paymentToken, $idempotencyKey);

        return response()->json($result, 201);
    }

    public function destroy(string $trackingNumber): JsonResponse
    {
        $result = Usps::labels()->cancelLabel($trackingNumber);

        return response()->json($result);
    }
}
