<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CreateScanFormRequest;

class ScanFormController extends Controller
{
    public function store(CreateScanFormRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $scanFormType = $validated['scanFormType'];

        unset($validated['scanFormType']);

        $result = match ($scanFormType) {
            'LABEL_SHIPMENT' => Usps::scanForms()->createLabelShipment($validated),
            'MID_SHIPMENT' => Usps::scanForms()->createMidShipment($validated),
            'MANIFEST_MID_SHIPMENT' => Usps::scanForms()->createManifestMidShipment($validated),
        };

        return response()->json($result, 201);
    }
}
