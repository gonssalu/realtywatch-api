<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class IndexPolygonPropertiesRequest extends FormRequest
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
            'p' => 'array|min:3|max:20',
            'p.*.x' => 'required|numeric|between:-90,90',
            'p.*.y' => 'required|numeric|between:-180,180',
        ];
    }
}
