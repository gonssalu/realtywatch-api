<?php

namespace App\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        //ATTENTION: Same as in app\Http\Requests\Property\IndexPolygonPropertiesRequest.php
        return [
            'query' => 'string',
            'include_tags' => 'json',
            'exclude_tags' => 'json',
            'adm_id' => 'integer',
            'list_id' => 'integer',
            //type
            't' => 'array',
            't.*' => 'string|in:house,apartment,office,shop,warehouse,garage,land,other',
            //listing_type
            'lt' => 'array',
            'lt.*' => 'string|in:both,sale,rent,none',
            //status
            's' => 'array',
            's.*' => 'string|in:available,unavailable,unknown',
            'price_min' => 'integer',
            'price_max' => 'integer',
            'area_min' => 'integer',
            'area_max' => 'integer',
            'rating_min' => 'integer',
            'rating_max' => 'integer',
            'wc' => 'integer',
            //typology
            'tl' => 'array',
            'tl.*' => ['string', Rule::in(['T0', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6+'])],
            'address' => 'string',
        ];
    }
}
