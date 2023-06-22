<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /*
    * Get the error messages for the defined validation rules.
    */
    public function messages()
    {
        return [
            'lists.*.exists' => 'The selected list is invalid or does not belong to the current user.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user_id = $this->user()->id;

        //When changing tag stuff also change CreateTagRequest
        return [
            /* MAIN STUFF */
            'title' => 'required|string|min:3|max:200',
            'type' => 'required|in:available,unavailable,unknown',
            'status' => 'required|in:house,apartment,office,shop,warehouse,garage,land,other',
            'description' => 'string|max:5000',
            'typology' => 'string|max:12',
            'gross_area' => 'integer|min:0',
            'useful_area' => 'integer|min:0',
            'wc' => 'integer|min:0',
            'rating' => 'integer|min:0|max:10',
            /* TAGS */
            'tags' => [
                'array',
                'max:10',
            ],
            'tags.*' => 'required|string|lowercase|min:1|max:32',
            /* LISTS */
            'lists' => 'array',
            'lists.*' => [
                'required',
                'integer',
                Rule::exists('lists', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }),
            ],
        ];
    }
}
