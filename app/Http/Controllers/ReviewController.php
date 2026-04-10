<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Review;

class ReviewController extends Controller
{
    public function create(Venue $venue): mixed
    {
        $user = auth()->user();

        if ($user && !Review::userCanReviewVenue($user, $venue)) {
            $nextDate = Review::userNextReviewDate($user, $venue);

            return redirect()
                ->route('venues.show', $venue)
                ->with('review_blocked', 'You have already reviewed this venue recently. You can review it again after ' . $nextDate->format('j F Y') . '.');
        }

        return view('reviews.create', compact('venue'));
    }

    public function myReviews(): View
    {
        $reviews = auth()->user()
            ->reviews()
            ->with(['venue', 'scores', 'tags'])
            ->latest()
            ->paginate(10);

        return view('reviews.my', compact('reviews'));
    }
}
