<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;

class SearchPropertyRequest extends FormRequest
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
            'query' => 'string|required_without_all:tags,adm_id',
            'tags' => 'json|required_without_all:query,adm_id',
            'adm_level' => 'integer|between:1,3|required_with:adm_id',
            'adm_id' => 'integer|required_without_all:query,tags',
        ];
    }
}
