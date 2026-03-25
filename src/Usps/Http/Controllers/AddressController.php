<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CityStateLookupRequest;
use Johnpaulmedina\Usps\Http\Requests\ValidateAddressRequest;
use Johnpaulmedina\Usps\Http\Requests\ZipCodeLookupRequest;

class AddressController extends Controller
{
    public function validate(ValidateAddressRequest $request): JsonResponse
    {
        $result = Usps::addressLookup($request->validated());

        return response()->json($result);
    }

    public function cityState(CityStateLookupRequest $request): JsonResponse
    {
        $result = Usps::cityStateLookup($request->validated()['ZIPCode']);

        return response()->json($result);
    }

    public function zipcode(ZipCodeLookupRequest $request): JsonResponse
    {
        $result = Usps::zipCodeLookup($request->validated());

        return response()->json($result);
    }
}
