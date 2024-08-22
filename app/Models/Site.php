<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    use HasFactory;
    protected $fillable=[
        "name",
        "description",
        "address",
        "radius",
        "longitude",
        "latitude",
        "gmt",
        "compagny_id"
    ];

    public function sensors(): HasMany {
        return $this->hasMany(Sensor::class, "site_id");
    }

    public function userSite(): HasOne {
        return $this->hasOne(UserSite::class, "site_id");
    }

    public function configuration(): HasOne {
        return $this->hasOne(Configuration::class);
    }


    public function compagny(): BelongsTo {
        return $this->belongsTo(Compagny::class, "compagny_id");
    }
}
