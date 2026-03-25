<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DropoffLocationsRequest extends FormRequest
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
            'destinationZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'destinationZIPPlus4' => ['nullable', 'string', 'regex:/^\d{4}$/'],
            'mailClass' => ['nullable', 'string'],
            'processingCategory' => ['nullable', 'string'],
            'destinationEntryFacilityType' => ['nullable', 'string'],
            'sortType' => ['nullable', 'string'],
            'containerTopology' => ['nullable', 'string'],
        ];
    }
}
