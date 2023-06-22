<?php

namespace App\Http\Requests\Tag;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTagRequest extends FormRequest
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
        $nameRules = 'string|lowercase|min:1|max:32';

        //When changing this also change StorePropertyRequest
        //TODO: test client max number of tags per request
        return [
            'name' => "$nameRules|required_without:names",
            'names' => [
                'array',
                'min:1',
                'max:10',
                'required_without:name'
            ],
            'names.*' => "$nameRules|required",
        ];
    }
}