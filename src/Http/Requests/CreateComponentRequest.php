<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $name
 * @property ?string $description
 * @property ?ComponentStatusEnum $status
 * @property ?string $link
 * @property ?int $order
 * @property ?bool $enabled
 * @property ?int $component_group_id
 */
class CreateComponentRequest extends FormRequest
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
            'name' => ['string', 'required', 'max:255'],
            'description' => ['string'],
            'status' => [Rule::enum(ComponentStatusEnum::class)],
            'link' => ['string'],
            'order' => ['int', 'min:0'],
            'enabled' => ['boolean'],
            'component_group_id' => ['int', 'min:0', Rule::exists('component_groups', 'id')],
        ];
    }
}
