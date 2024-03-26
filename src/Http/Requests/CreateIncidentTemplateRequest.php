<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateIncidentTemplateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['string'],
            'template' => ['required', 'string'],
            'engine' => [Rule::enum(IncidentTemplateEngineEnum::class)],
        ];
    }
}
