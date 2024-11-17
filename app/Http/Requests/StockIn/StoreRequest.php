<?php

namespace App\Http\Requests\StockIn;

use Illuminate\Foundation\Http\FormRequest;

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
            'stock_in_no' => ['required','string','max:128','unique:stock_ins,stock_in_no'],
            'stock_in_date' => ['required','date'],
            'due_date' => ['required','date'],
            'supplier_id' => ['required','integer','exists:suppliers,id'],
            'outlet_id' => ['required','integer','exists:outlets,id'],
            'user_id' => ['required','integer','exists:users,id'],
            'product_id' => ['required','array'],
            'qty' => ['required','array'],
            'price' => ['required','array'],
            'product_id.*' => ['required','integer','exists:products,id'],
            'qty.*' => ['required','integer','min:1'],
            'price.*' => ['required','numeric', 'min:0'],
        ];
    }
}
