<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStandardsRequest extends FormRequest
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
            'mailClass' => ['nullable', 'string'],
            'destinationType' => ['nullable', 'string'],
            'serviceTypeCodes' => ['nullable', 'string'],
            'destinationEntryFacilityType' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'dimensionsUOM' => ['nullable', 'string'],
        ];
    }
}
