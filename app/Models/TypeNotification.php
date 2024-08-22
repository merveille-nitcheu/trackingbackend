<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeNotification extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "code",
        "wording",

    ];


    public function notifications(): HasMany {
        return $this->hasMany(Notification::class);
    }
}
