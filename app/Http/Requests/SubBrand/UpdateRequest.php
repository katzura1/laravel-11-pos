<?php

namespace App\Http\Requests\SubBrand;

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
            'id' => ['required', 'exists:sub_brands,id'],
            'name' => ['required','string','unique:sub_brands,name,'.$this->id],
            'brand_id' => ['required','exists:brands,id'],
        ];
    }
}
