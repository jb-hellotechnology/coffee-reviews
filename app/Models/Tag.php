<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tag extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'slug', 'category'];

    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class);
    }
}
