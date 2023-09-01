<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentRequest extends FormRequest
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
            'message' => ['string'],
            'status' => [Rule::enum(IncidentStatusEnum::class)],
            'visible' => ['boolean'],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            'occurred_at' => ['nullable', 'string'],
        ];
    }
}
