<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use App\Services\CoffeeScoreService;

class Venue extends Model
{
    use HasUuids, Searchable;

    protected $fillable = [
        'roaster_id',
        'suggested_by',
        'google_place_id',
        'name',
        'slug',
        'address',
        'city',
        'postcode',
        'lat',
        'lng',
        'phone',
        'website',
        'opening_hours',
        'coffee_score',
        'review_count',
        'verified',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'verified'      => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function toSearchableArray(): array
    {
        // Gather all AI tags from reviews for this venue
        $tags = $this->reviews()
            ->whereNotNull('ai_tags')
            ->pluck('ai_tags')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'city'         => $this->city,
            'postcode'     => $this->postcode,
            'address'      => $this->address,
            'coffee_score' => $this->coffee_score,
            'review_count' => $this->review_count,
            'tags'         => implode(' ', $tags),
        ];
    }

    public function roaster(): BelongsTo
    {
        return $this->belongsTo(Roaster::class);
    }

    public function suggestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suggested_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function recalculateCoffeeScore(): void
    {
        app(CoffeeScoreService::class)->recalculate($this);
    }
}
