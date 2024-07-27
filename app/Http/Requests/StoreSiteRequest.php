<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "address" => "nullable|string",
            "radius" => "decimal:0,4|max:100000|min:1",
            "longitude" => "nullable|decimal:0,10",
            "latitude" => "nullable|decimal:0,10",
            "gmt" => "required|decimal:0,4",
            "compagny_id" => "required|integer|min:1|exists:compagnies,id"
        ];
    }
}
