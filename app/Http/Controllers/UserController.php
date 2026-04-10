<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(User $user): View
    {
        $reviews = $user->reviews()
            ->with(['venue', 'scores', 'tags'])
            ->latest()
            ->paginate(10);

        // Aggregate stats across all reviews
        $stats = [
            'total_reviews'  => $user->reviews()->count(),
            'venues_reviewed' => $user->reviews()->distinct('venue_id')->count('venue_id'),
            'avg_espresso'   => $user->reviews()->join('review_scores', 'reviews.id', '=', 'review_scores.review_id')->avg('espresso'),
            'top_tags'       => \App\Models\Tag::whereHas('reviews', fn($q) =>
                                    $q->where('user_id', $user->id)
                                )->withCount(['reviews' => fn($q) =>
                                    $q->where('user_id', $user->id)
                                ])->orderByDesc('reviews_count')->take(8)->get(),
        ];

        return view('users.show', compact('user', 'reviews', 'stats'));
    }
}
