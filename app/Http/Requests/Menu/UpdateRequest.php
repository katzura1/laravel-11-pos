<?php

namespace App\Http\Requests\Menu;

use Illuminate\Validation\Validator;
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
            'id' => ['required', 'integer', 'exists:menus,id'],
            "name" => ["required","string"],
            'url' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'position' => ['required', 'integer'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->id == $this->parent_id) {
                $validator->errors()->add('parent_id', 'The parent ID cannot be the same as the ID.');
            }
        });
    }
}
