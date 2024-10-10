<?php

namespace Cachet\Http\Requests;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateIncidentRequest extends FormRequest
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
            'message' => ['required_without:template', 'string'],
            'template' => ['required_without:message', 'string'],
            'status' => ['required', Rule::enum(IncidentStatusEnum::class)],
            'visible' => ['boolean'],
            'stickied' => ['boolean'],
            'notifications' => ['boolean'],
            'occurred_at' => ['nullable', 'string'],
            'template_vars' => ['array'],
            'component_id' => [Rule::exists('components', 'id')],
            'component_status' => ['nullable', Rule::enum(ComponentStatusEnum::class), 'required_with:component_id'],
        ];
    }

    /**
     * Get data to be validated from the request.
     */
    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'template_vars' => $this->input('template_vars', []),
            'visible' => $this->boolean('visible'),
            'notifications' => $this->boolean('notifications'),
            'stickied' => $this->boolean('stickied'),
            'component_status' => $this->enum('component_status', ComponentStatusEnum::class),
        ]);
    }
}
