<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\PaymentAuthorizationRequest;

class PaymentController extends Controller
{
    public function authorize(PaymentAuthorizationRequest $request): JsonResponse
    {
        $result = Usps::payments()->createPaymentAuthorization($request->validated());

        return response()->json($result, 201);
    }
}
