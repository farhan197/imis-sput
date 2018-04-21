<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $buyer = $this->route('buyer');

        return [
            'name' => [
                'required',
                Rule::unique('buyers')->ignore($buyer ? $buyer->id : 0)
            ]
        ];
    }
}
