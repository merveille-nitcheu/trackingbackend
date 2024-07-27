<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compagny extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'address',
        'website_link',
        'contact'
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class, 'compagny_id');
    }

    public function sites(): HasMany {
        return $this->hasMany(Site::class, "compagny_id");
    }
}
