<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingOptionsSearchRequest extends FormRequest
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
            'originZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'destinationZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'weight' => ['required', 'numeric', 'min:0'],
            'length' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'mailClass' => ['nullable', 'string'],
            'processingCategory' => ['nullable', 'string'],
            'rateIndicator' => ['nullable', 'string'],
            'destinationEntryFacilityType' => ['nullable', 'string'],
            'priceType' => ['nullable', 'string'],
            'accountType' => ['nullable', 'string'],
            'accountNumber' => ['nullable', 'string'],
            'acceptanceDate' => ['nullable', 'date_format:Y-m-d'],
            'pricingOptions' => ['nullable', 'array'],
        ];
    }
}
