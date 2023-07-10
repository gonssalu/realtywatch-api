<?php

namespace App\Http\Requests\Characteristic;

use Illuminate\Foundation\Http\FormRequest;

class CharacteristicStoreUpdateRequest extends FormRequest
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
            'name' => 'required|string|min:1|max:30',
            'type' => 'required|string|in:numerical,textual',
        ];
    }
}
