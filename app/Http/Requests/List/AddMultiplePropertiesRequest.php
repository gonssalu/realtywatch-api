<?php

namespace App\Http\Requests\List;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMultiplePropertiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'properties.*.exists' => 'The selected property is invalid or does not belong to the current user.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $user_id = $this->user()->id;

        return [
            'properties' => 'required|array',
            'properties.*' => [
                'required', 'integer',
                Rule::exists('properties', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                }),
            ],
        ];
    }
}
