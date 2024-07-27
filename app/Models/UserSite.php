<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSite extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable=[
        "user_id",
        "site_id"
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id");
    }

    public function site(): BelongsTo{
        return $this->belongsTo(Site::class, "site_id");
    }
}
