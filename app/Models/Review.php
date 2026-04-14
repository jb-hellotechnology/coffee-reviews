<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Jobs\AnalyseReviewWithAI;
use Illuminate\Support\Facades\Storage;
use App\Jobs\AnalyseReviewPhoto;

class Review extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'venue_id',
        'body',
        'ai_analysed',
        'ai_tags',
        'verified',
        'photo',
        'photo_alt',
        'photo_analysed',
        'moderation_action',
        'moderation_reason',
    ];

    protected $casts = [
        'ai_tags'     => 'array',
        'ai_analysed' => 'boolean',
        'verified'    => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (Review $review) {
            AnalyseReviewWithAI::dispatch($review);

            if ($review->photo) {
                AnalyseReviewPhoto::dispatch($review);
            }
        });

        static::saved(function (Review $review) {
            $review->venue->recalculateCoffeeScore();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function scores(): HasOne
    {
        return $this->hasOne(ReviewScore::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public static function userCanReviewVenue(User $user, Venue $venue): bool
    {
        return !static::where('user_id', $user->id)
            ->where('venue_id', $venue->id)
            ->where('created_at', '>=', now()->subMonths(3))
            ->exists();
    }

    public static function userNextReviewDate(User $user, Venue $venue): ?\Carbon\Carbon
    {
        $latest = static::where('user_id', $user->id)
            ->where('venue_id', $venue->id)
            ->latest()
            ->first();

        return $latest?->created_at->addMonths(3);
    }

    public function photoUrl(): ?string
    {
        if ($this->photo) {
            return Storage::url($this->photo);
        }
        return null;
    }
}
