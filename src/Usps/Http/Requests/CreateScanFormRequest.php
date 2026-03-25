<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScanFormRequest extends FormRequest
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
            'scanFormType' => ['required', 'string', 'in:LABEL_SHIPMENT,MID_SHIPMENT,MANIFEST_MID_SHIPMENT'],
            'trackingNumbers' => ['required_if:scanFormType,LABEL_SHIPMENT', 'array'],
            'trackingNumbers.*' => ['string', 'max:34'],
            'MID' => ['required_if:scanFormType,MID_SHIPMENT', 'nullable', 'string'],
            'manifestMID' => ['required_if:scanFormType,MANIFEST_MID_SHIPMENT', 'nullable', 'string'],
            'overrideAddress' => ['nullable', 'array'],
            'overrideAddress.streetAddress' => ['required_with:overrideAddress', 'string', 'max:255'],
            'overrideAddress.city' => ['required_with:overrideAddress', 'string', 'max:100'],
            'overrideAddress.state' => ['required_with:overrideAddress', 'string', 'size:2'],
            'overrideAddress.ZIPCode' => ['required_with:overrideAddress', 'string', 'regex:/^\d{5}$/'],
        ];
    }
}
