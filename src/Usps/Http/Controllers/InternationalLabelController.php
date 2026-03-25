<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CreateInternationalLabelRequest;

class InternationalLabelController extends Controller
{
    public function store(CreateInternationalLabelRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $paymentToken = $validated['paymentToken'];
        $idempotencyKey = $validated['idempotencyKey'] ?? null;

        unset($validated['paymentToken'], $validated['idempotencyKey']);

        $result = Usps::internationalLabels()->createLabel($validated, $paymentToken, $idempotencyKey);

        return response()->json($result, 201);
    }
}
