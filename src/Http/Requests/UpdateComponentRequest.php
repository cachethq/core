<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComponentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'description' => ['string'],
            'status' => [Rule::enum(ComponentStatusEnum::class)],
            'link' => ['string'],
            'order' => ['int', 'min:0'],
            'component_group_id' => ['int', 'min:0', Rule::exists('component_groups', 'id')],
            'enabled' => ['boolean'],
        ];
    }
}
