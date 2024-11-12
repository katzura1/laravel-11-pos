<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:products,id'],
            'code' => ['required', 'string', 'max:128', 'unique:products,code,' . $this->id],
            'name' => ['required', 'string', 'max:128'],
            'sub_brand_id' => ['required', 'integer', 'exists:sub_brands,id'],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'class' => ['required', 'string', 'max:128'],
            'buying_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
