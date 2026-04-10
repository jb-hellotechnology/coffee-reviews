<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Roaster extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'slug', 'city', 'website', 'description'];

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }
}
