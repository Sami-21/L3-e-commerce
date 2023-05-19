<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'client_id' => 'exists:clients,id',
            // 'firstname' =>
            // 'string',
            // 'lastname' => 'string',
            // 'address' => 'required|string',
            // 'wilaya' => 'required|string',
            // 'city' => 'required|string',
            // 'delivery_date' => 'required|string',
            // 'phone' => 'required|string',
            // 'products' => 'required',
            // 'products.*.id' => 'required|exists:products,id',
            // 'products.*.quantity' => 'required|integer|min:1',
            // 'products.*.price' => 'required|numeric|min:0',
            // 'total' => 'required|numeric|min:0',
        ];
    }
}
