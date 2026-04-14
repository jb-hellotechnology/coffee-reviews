<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

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

    public function edit(Review $review): View
    {
        // Only the review author or an admin can edit
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|min:30|max:2000',
        ]);

        $review->update([
            'body'        => $request->body,
            'ai_analysed' => false,
        ]);

        // Re-run AI tagging on the updated text
        \App\Jobs\AnalyseReviewWithAI::dispatch($review);

        return redirect()
            ->route('venues.show', $review->venue)
            ->with('success', 'Review updated successfully.');
    }
}
