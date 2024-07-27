<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "user_id" => "required|integer|min:1|exists:users,id",
            'name' => 'required|string|max:250',
            'email' => 'required|email',
            'address' => 'required|string|max:250',
            'contact' => 'required|string|max:250',
            'compagny_id' => 'required|integer|min:1|exists:compagnies,id',
            'created_by' => 'required|integer|min:1|exists:users,id',
        ];
    }
}
