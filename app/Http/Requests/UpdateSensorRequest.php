<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSensorRequest extends FormRequest
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
            "sensor_id" => "required|integer|min:1|exists:sensors,id",
            "sensor_reference" => "nullable|string|max:250",
            "description" => "nullable|string|max:250",
            "site_id" => "nullable|integer|min:1|exists:sites,id"

        ];
    }
}
