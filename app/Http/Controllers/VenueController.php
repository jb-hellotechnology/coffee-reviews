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

    public function city(string $city): View
    {
        $venues = Venue::query()
            ->whereRaw('LOWER(city) = ?', [strtolower($city)])
            ->with(['reviews.scores'])
            ->orderByDesc('coffee_score')
            ->get();

        if ($venues->isEmpty() && !Venue::whereRaw('LOWER(city) = ?', [strtolower($city)])->exists()) {
            abort(404);
        }

        $cityName = $venues->first()?->city ?? ucfirst($city);

        $dimensions = [
            'espresso'          => 'Espresso',
            'milk_work'         => 'Milk work',
            'filter_options'    => 'Filter',
            'bean_sourcing'     => 'Bean sourcing',
            'barista_knowledge' => 'Barista knowledge',
            'equipment'         => 'Equipment',
            'decaf_available'   => 'Decaf',
            'value'             => 'Value',
        ];

        return view('venues.city', compact('venues', 'cityName', 'dimensions'));
    }
}
