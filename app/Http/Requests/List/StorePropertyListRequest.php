<?php

namespace App\Http\Requests\List;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|min:1|max:100|required',
            'description' => 'string|max:5000',
        ];
    }
}
