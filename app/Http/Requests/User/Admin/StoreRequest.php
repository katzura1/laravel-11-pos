<?php

namespace App\Http\Requests\User\Admin;

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
            'name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8' , 'max:25'],
            'outlet_id' => ['required', 'array', 'min:1'],
            'outlet_id.*' => ['required', 'integer', 'exists:outlets,id'],
        ];
    }
}
