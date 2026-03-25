<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\ShippingOptionsSearchRequest;

class ShippingOptionController extends Controller
{
    public function search(ShippingOptionsSearchRequest $request): JsonResponse
    {
        $result = Usps::shippingOptions()->search($request->validated());

        return response()->json($result);
    }
}
