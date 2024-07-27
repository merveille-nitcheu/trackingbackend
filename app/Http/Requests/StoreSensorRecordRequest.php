<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorRecordRequest extends FormRequest
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
            "longitude" => "nullable|decimal:0,10",
            "latitude" => "nullable|decimal:0,10",
            "temperature" => "nullable|decimal:0,4",
            "battery" => "nullable|decimal:0,4"
        ];
    }
}
