<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Venue;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Mail\ReviewDeletedMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\VenueVerifiedMail;
use App\Models\Roaster;


class AdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_venues'  => Venue::count(),
            'total_reviews' => Review::count(),
            'total_users'   => User::count(),
            'pending_venues' => Venue::where('verified', false)->count(),
            'flagged_reviews' => Review::where('verified', false)->count(),
        ];

        return view('admin.index', compact('stats'));
    }

    // Venues
    public function venues(): View
    {
        $venues = Venue::with(['reviews', 'suggestedBy'])
            ->orderBy('verified')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.venues', compact('venues'));
    }

    public function verifyVenue(Venue $venue): RedirectResponse
    {
        $venue->update(['verified' => true]);

        if ($venue->suggestedBy) {
            Mail::to($venue->suggestedBy)->send(new VenueVerifiedMail($venue->suggestedBy, $venue));
        }

        return back()->with('success', "{$venue->name} has been verified and the submitter notified.");
    }

    public function deleteVenue(Venue $venue): RedirectResponse
    {
        $name = $venue->name;
        $venue->delete();

        return redirect()
            ->route('admin.venues')
            ->with('success', "{$name} has been deleted.");
    }

    // Reviews
    public function reviews(): View
    {
        $reviews = Review::with(['user', 'venue'])
            ->orderByRaw("FIELD(moderation_action, 'flag', 'reject', 'pending', 'approve')")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function approveReview(Review $review): RedirectResponse
    {
        $review->update(['verified' => true]);

        $review->venue->recalculateCoffeeScore();

        return back()->with('success', 'Review approved.');
    }

    public function deleteReview(Review $review): RedirectResponse
    {
        $venue      = $review->venue;
        $user       = $review->user;
        $venueName  = $venue->name;
        $reviewBody = $review->body;

        $review->delete();
        $venue->recalculateCoffeeScore();

        Mail::to($user)->send(new ReviewDeletedMail($user, $venueName, $reviewBody));

        return back()->with('success', 'Review deleted and user notified.');
    }

    // Users
    public function users(): View
    {
        $users = User::withCount('reviews')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function banUser(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot ban an admin user.');
        }

        // Delete all their reviews and recalculate affected venues
        $user->reviews->each(function (Review $review) {
            $venue = $review->venue;
            $review->delete();
            $venue->recalculateCoffeeScore();
        });

        $user->delete();

        return back()->with('success', 'User banned and reviews removed.');
    }

    public function roasters(): View
    {
        $roasters = Roaster::withCount('venues')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.roasters', compact('roasters'));
    }

    public function toggleCoffeeExpert(User $user): RedirectResponse
    {
        $user->update(['is_coffee_expert' => !$user->is_coffee_expert]);

        $status = $user->is_coffee_expert ? 'awarded' : 'removed';

        return back()->with('success', "Coffee Expert badge {$status} for {$user->name}.");
    }
}
