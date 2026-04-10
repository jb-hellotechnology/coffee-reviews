<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $city = $request->input('city');

        $cities = Venue::select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $venues = Venue::query()
            ->when($city, fn($q) => $q->where('city', $city))
            ->where('review_count', '>', 0)
            ->orderByDesc('coffee_score')
            ->paginate(20);

        return view('venues.index', compact('venues', 'cities', 'city'));
    }

    public function create(): View
    {
        return view('venues.create');
    }

    public function show(Venue $venue): View
    {
        $venue->load(['reviews.scores', 'reviews.user', 'roaster']);

        return view('venues.show', compact('venue'));
    }

    public function map(): View
    {
        $venues = Venue::query()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get(['id', 'name', 'slug', 'address', 'city', 'lat', 'lng', 'coffee_score', 'review_count']);

        return view('venues.map', compact('venues'));
    }
}
