<?php

declare(strict_types=1);

namespace Cachet\Http\Requests;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleRequest extends FormRequest
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
            'scheduled_at' => ['nullable', 'date'],
            'components' => ['array'],
            'components.*.id' => ['required_with:components', 'int', 'exists:components,id'],
            'components.*.status' => ['required_with:components', 'int', Rule::enum(ComponentStatusEnum::class)],
        ];
    }
}
