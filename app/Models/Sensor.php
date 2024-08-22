<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;
    protected $fillable = [
        "sensor_reference",
        "description",
        "site_id"
    ];

    public function sensorRecords(): HasMany {
        return $this->hasMany(SensorRecord::class, "sensor_id");
    }

   /*  public function lastSensorRecords(): HasMany{
        return $this->hasMany(SensorRecord::class, "sensor_id");
    } */

    public function site(): BelongsTo {
        return $this->belongsTo(Site::class, "site_id");
    }

    public function notifications(): HasMany {
        return $this->hasMany(Notification::class);
    }
}
