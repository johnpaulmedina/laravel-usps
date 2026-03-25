<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLabelRequest extends FormRequest
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
            'paymentToken' => ['required', 'string'],
            'idempotencyKey' => ['nullable', 'string', 'max:64'],
            'imageInfo' => ['required', 'array'],
            'imageInfo.imageType' => ['required', 'string'],
            'imageInfo.labelType' => ['required', 'string'],
            'toAddress' => ['required', 'array'],
            'toAddress.streetAddress' => ['required', 'string', 'max:255'],
            'toAddress.city' => ['required', 'string', 'max:100'],
            'toAddress.state' => ['required', 'string', 'size:2'],
            'toAddress.ZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'fromAddress' => ['required', 'array'],
            'fromAddress.streetAddress' => ['required', 'string', 'max:255'],
            'fromAddress.city' => ['required', 'string', 'max:100'],
            'fromAddress.state' => ['required', 'string', 'size:2'],
            'fromAddress.ZIPCode' => ['required', 'string', 'regex:/^\d{5}$/'],
            'packageDescription' => ['required', 'array'],
            'packageDescription.weight' => ['required', 'numeric', 'min:0.01'],
            'packageDescription.mailClass' => ['required', 'string'],
            'packageDescription.processingCategory' => ['required', 'string'],
            'packageDescription.rateIndicator' => ['required', 'string'],
        ];
    }
}
