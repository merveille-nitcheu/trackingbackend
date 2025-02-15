<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'batteryPercent' => ucfirst($this->batteryPercent),
            'sensor_reference' => ucfirst($this->sensor_reference),
            'created_at' => $this->created_at,
            // 'typeNotification' => optional($this->typeNotification->wording),
        ];
    }
}
