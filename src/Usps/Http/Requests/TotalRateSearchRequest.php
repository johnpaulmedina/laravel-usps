<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TotalRateSearchRequest extends FormRequest
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
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'mailClass' => ['required', 'string'],
            'processingCategory' => ['required', 'string'],
            'rateIndicator' => ['required', 'string'],
            'extraServices' => ['nullable', 'array'],
            'extraServices.*' => ['string'],
            'priceType' => ['nullable', 'string'],
        ];
    }
}
