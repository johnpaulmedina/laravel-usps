<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Johnpaulmedina\Usps\Facades\Usps;
use Johnpaulmedina\Usps\Http\Requests\CreateContainerRequest;

class ContainerController extends Controller
{
    public function store(CreateContainerRequest $request): JsonResponse
    {
        $result = Usps::containers()->createContainer($request->validated());

        return response()->json($result, 201);
    }
}
