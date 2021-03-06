<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialStockRequest extends FormRequest
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
        return [
            'dumping_date' => 'required|date',
            'stock_area_id' => 'required',
            'customer_id' => 'required',
            'contractor_id' => 'required',
            'material_type' => 'required',
            'volume' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'dumping_date' => 'Dumping Date',
            'stock_area_id' => 'Stock Area',
            'customer_id' => 'Customer',
            'contractor_id' => 'Contractor',
            'material_type' => 'Material Type',
            'volume' => 'Volume',
            'seam_id' => 'Seam'
        ];
    }
}
