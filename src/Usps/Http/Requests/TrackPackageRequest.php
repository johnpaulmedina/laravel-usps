<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackPackageRequest extends FormRequest
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
            '*.trackingNumber' => ['required', 'string', 'max:34'],
            '*.mailingDate' => ['nullable', 'date_format:Y-m-d'],
            '*.destinationZIPCode' => ['nullable', 'string', 'regex:/^\d{5}$/'],
        ];
    }
}
