<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePickupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'etag' => ['required', 'string'],
            'pickupDate' => ['required', 'date_format:Y-m-d'],
            'pickupAddress' => ['required', 'array'],
            'pickupAddress.streetAddress' => ['required', 'string', 'max:255'],
            'pickupAddress.city' => ['required', 'string', 'max:100'],
            'pickupAddress.state' => ['required', 'string', 'size:2'],
            'pickupAddress.ZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.packageType' => ['required', 'string'],
            'packages.*.packageCount' => ['required', 'integer', 'min:1'],
            'estimatedWeight' => ['required', 'numeric', 'min:0.01'],
            'pickupLocation' => ['required', 'array'],
            'pickupLocation.packageLocation' => ['required', 'string'],
        ];
    }
}
