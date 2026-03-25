<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceEstimatesRequest extends FormRequest
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
            'originZIPCode' => ['required', 'string', 'regex:/^\d{3,9}$/'],
            'destinationZIPCode' => ['required', 'string', 'regex:/^\d{5,9}$/'],
            'acceptanceDate' => ['nullable', 'date_format:Y-m-d'],
            'acceptanceTime' => ['nullable', 'string'],
            'mailClass' => ['nullable', 'string'],
            'destinationType' => ['nullable', 'string'],
            'serviceTypeCodes' => ['nullable', 'string'],
            'destinationEntryFacilityType' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'weightUOM' => ['nullable', 'string'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'dimensionsUOM' => ['nullable', 'string'],
        ];
    }
}
