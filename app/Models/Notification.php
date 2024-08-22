<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "sensor_id",
        "sensor_reference",
        "typeNotification_id",
        "batteryPercent",
        "description",

    ];

    public function typeNotification(): BelongsTo {
        return $this->belongsTo(TypeNotification::class, "typeNotifcation_id");
    }

    public function sensor(): BelongsTo {
        return $this->belongsTo(Sensor::class, "sensor_id");
    }
}
