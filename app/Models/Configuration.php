<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuration extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "color",
        "zoom",
        "site_id",
        "trakerType",

    ];


    public function site(): BelongsTo {
        return $this->BelongsTo(Site::class, "site_id");
    }
}
