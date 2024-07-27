<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        "sensor_id",
        "longitude",
        "latitude",
        "temperature",
        "battery"
    ];

    public function sensor(): BelongsTo {
        return $this->belongsTo(Sensor::class, "sensor_id");
    }
}
