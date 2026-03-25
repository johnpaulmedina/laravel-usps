<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProofOfDeliveryRequest extends FormRequest
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
            'uniqueTrackingID' => ['required', 'string', 'max:50'],
            'mailingDate' => ['nullable', 'date_format:Y-m-d'],
            'letterType' => ['nullable', 'string'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*.email' => ['required', 'email', 'max:255'],
            'recipients.*.firstName' => ['required', 'string', 'max:100'],
            'recipients.*.lastName' => ['required', 'string', 'max:100'],
            'CRID' => ['nullable', 'string', 'max:20'],
        ];
    }
}
