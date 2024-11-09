<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'identity_number' => ['required', 'string', 'size:16', 'unique:customers,identity_number,' . $this->input('id')],
            'phone_number' => ['required', 'string','min:10' ,'max:16','regex:/^(?:\+62|62|0)[0-9]{9,}$/', 'unique:customers,phone_number,' . $this->input('id')],
            'birth_date' => ['required', 'date'],
        ];
    }
}
