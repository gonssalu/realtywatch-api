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
            'media.images.*.max' => 'The image may not be greater than 10MB.',
            'media.videos.*.max' => 'The video may not be greater than 100MB.',
            'media.blueprints.*.max' => 'The blueprint may not be greater than 10MB.',
            'media.images.*.mimetypes' => 'The image must be a file of type: jpeg, png, webp, gif.',
            'media.videos.*.mimetypes' => 'The video must be a file of type: mp4, webm, h264, 3gp.',
            'media.blueprints.*.mimetypes' => 'The blueprint must be a file of type: jpeg, png, webp, pdf.',
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
            'type' => 'required|in:house,apartment,office,shop,warehouse,garage,land,other',
            'status' => 'required|in:available,unavailable,unknown',
            'description' => 'string|max:5000',
            'typology' => 'string|max:12',
            'gross_area' => 'integer|min:0',
            'useful_area' => 'integer|min:0',
            'wc' => 'integer|min:0|max:100',
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

            /* ADDRESS */
            'address' => 'required|array',
            'address.adm1_id' => [
                'integer',
                'required_without:full_address', // Required without full_address
                Rule::exists('administrative_divisions', 'id')->where(function ($query) {
                    $query->where('level', 1);
                }),
            ],
            'address.adm2_id' => [
                'integer',
                Rule::exists('administrative_divisions', 'id')->where(function ($query) {
                    $query->where('level', 2);
                }),
            ],
            'address.adm3_id' => [
                'integer',
                Rule::exists('administrative_divisions', 'id')->where(function ($query) {
                    $query->where('level', 3);
                }),
            ],
            'address.postal_code' => 'string|min:4|max:10',
            'address.full_address' => 'required_without:adm1_id|string|max:500', // Required without adm1_id
            'address.latitude' => 'numeric|between:-90,90',
            'address.longitude' => 'numeric|between:-180,180',
        ];
    }
}
