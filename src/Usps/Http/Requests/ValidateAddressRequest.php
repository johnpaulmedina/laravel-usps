<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAddressRequest extends FormRequest
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
            'streetAddress' => ['required', 'string', 'max:255'],
            'secondaryAddress' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'ZIPCode' => ['nullable', 'string', 'regex:/^\d{5}$/'],
            'ZIPPlus4' => ['nullable', 'string', 'regex:/^\d{4}$/'],
            'firm' => ['nullable', 'string', 'max:255'],
        ];
    }
}
