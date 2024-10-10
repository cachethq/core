<?php

namespace Cachet\Http\Requests;

use Cachet\Rules\FactorOfSixty;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMetricRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'suffix' => ['string', 'max:255'],
            'description' => ['string'],
            'default_value' => ['float'],
            'threshold' => ['int', 'min:0', 'max:60', new FactorOfSixty],
        ];
    }
}
