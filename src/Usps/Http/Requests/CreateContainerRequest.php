<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateContainerRequest extends FormRequest
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
            'containerType' => ['required', 'string'],
            'destinationEntryFacilityType' => ['required', 'string'],
            'destinationZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'trackingNumbers' => ['nullable', 'array'],
            'trackingNumbers.*' => ['string', 'max:34'],
        ];
    }
}
