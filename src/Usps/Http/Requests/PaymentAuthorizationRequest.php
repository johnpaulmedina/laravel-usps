<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentAuthorizationRequest extends FormRequest
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
            'roles' => ['required', 'array', 'min:1'],
            'roles.*.roleName' => ['required', 'string'],
            'roles.*.CRID' => ['nullable', 'string'],
            'roles.*.MID' => ['nullable', 'string'],
            'roles.*.manifestMID' => ['nullable', 'string'],
            'roles.*.accountType' => ['nullable', 'string'],
            'roles.*.accountNumber' => ['nullable', 'string'],
            'roles.*.permitNumber' => ['nullable', 'string'],
            'roles.*.permitZIP' => ['nullable', 'string'],
        ];
    }
}
