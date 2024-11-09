<?php

namespace App\Http\Requests\User\Cashier;

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
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required', 'string', 'max:128', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:25'],
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
        ];
    }
}