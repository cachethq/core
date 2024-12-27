<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

    /**
     * Specify body parameter documentation for Scribe.
     */
    public function bodyParameters(): array
    {
        return [
            'status' => [
                'description' => 'The status of the component. See [Component Statuses](/v3.x/guide/components#component-statuses) for more information.',
                'example' => '1',
                'required' => false,
                'schema' => [
                    'type' => 'integer',
                    'enum' => ComponentStatusEnum::cases(),
                ],
            ],
        ];
    }
}
