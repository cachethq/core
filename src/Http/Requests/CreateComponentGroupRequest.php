<?php

namespace Cachet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateComponentGroupRequest extends FormRequest
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
            'order' => ['int', 'min:0'],
            'visible' => ['bool'],
        ];
    }
}
