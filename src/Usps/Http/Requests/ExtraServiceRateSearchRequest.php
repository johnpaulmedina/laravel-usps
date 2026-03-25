<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtraServiceRateSearchRequest extends FormRequest
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
            'mailClass' => ['required', 'string'],
            'extraService' => ['required', 'string'],
            'processingCategory' => ['nullable', 'string'],
            'rateIndicator' => ['nullable', 'string'],
            'priceType' => ['nullable', 'string'],
            'itemValue' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
