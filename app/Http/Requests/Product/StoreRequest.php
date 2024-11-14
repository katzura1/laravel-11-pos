<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile; // Add this line

class StoreRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:128', 'unique:products,code'],
            'name' => ['required', 'string', 'max:128'],
            'sub_brand_id' => ['required', 'integer', 'exists:sub_brands,id'],
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'class' => ['required', 'string', 'max:128'],
            'buying_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'image' => ['required', 'string', 'regex:/^data:image\/(jpeg|png|jpg|gif);base64,/', 'max:2097152'], // Update this line
        ];
    }
}
