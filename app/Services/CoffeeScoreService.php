<?php

namespace App\Services;

use App\Models\ReviewScore;
use App\Models\Venue;

class CoffeeScoreService
{
    private array $weights = [
        'espresso'          => 2.0,
        'bean_sourcing'     => 1.8,
        'filter_options'    => 1.5,
        'milk_work'         => 1.2,
        'barista_knowledge' => 1.2,
        'equipment'         => 1.0,
        'value'             => 0.8,
        'decaf_available'   => 0.5,
    ];

    public function recalculate(Venue $venue): void
    {
        $scores = ReviewScore::whereHas('review', fn($q) =>
            $q->where('venue_id', $venue->id)
              ->where('verified', true)
        )->get();

        // Count all verified reviews, not just those with scores
        $reviewCount = $venue->reviews()->where('verified', true)->count();

        if ($scores->isEmpty()) {
            $venue->update(['review_count' => $reviewCount]);
            return;
        }

        $perReviewScores = $scores->map(function ($score) {
            $weightedSum   = 0;
            $appliedWeight = 0;

            foreach ($this->weights as $field => $weight) {
                if (!is_null($score->$field)) {
                    $weightedSum   += $score->$field * $weight;
                    $appliedWeight += $weight;
                }
            }

            return $appliedWeight > 0
                ? ($weightedSum / $appliedWeight)
                : null;
        })->filter();

        $venue->update([
            'coffee_score' => $perReviewScores->isNotEmpty()
                ? round($perReviewScores->avg(), 2)
                : 0,
            'review_count' => $reviewCount,
        ]);
    }
}
