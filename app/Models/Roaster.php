<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Roaster extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'website',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Roaster $roaster) {
            if (empty($roaster->slug)) {
                $roaster->slug = Str::slug($roaster->name) . '-' . Str::random(4);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }
}
