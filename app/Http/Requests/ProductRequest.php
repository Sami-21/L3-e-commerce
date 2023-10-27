<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        switch ($this->getMethod()) {
            case "POST":
                return [
                    'name' => ['string', 'required', 'max:255'],
                    'price' => ['numeric', 'required'],
                    'promotion_price' => ['numeric', 'nullable'],
                    'features' => ['json', 'required'],
                    'images' => ['required'],
                    'category_id' => ['required']
                ];
            case "PUT":
                return [
                    'name' => ['string', 'required', 'max:255'],
                    'price' => ['numeric', 'required'],
                    'promotion_price' => ['numeric', 'required'],
                    'features' => ['json', 'required'],
                    'images' => ['required'],
                    'category_id' => ['required'],
                    'deleted_images' => ['array'],
                ];
        }
    }
}
