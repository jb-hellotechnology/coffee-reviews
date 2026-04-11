<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function edit(Venue $venue): View
    {
        $roasters = \App\Models\Roaster::orderBy('name')->get(['id', 'name']);
        return view('venues.edit', compact('venue', 'roasters'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string|max:255',
            'city'       => 'required|string|max:100',
            'postcode'   => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:30',
            'website'    => 'nullable|url|max:255',
            'roaster_id' => 'nullable|exists:roasters,id',
        ]);

        $venue->update([
            'name'       => $request->name,
            'address'    => $request->address,
            'city'       => $request->city,
            'postcode'   => $request->postcode,
            'phone'      => $request->phone,
            'website'    => $request->website,
            'roaster_id' => $request->roaster_id ?: null,
        ]);

        return redirect()
            ->route('venues.show', $venue)
            ->with('success', 'Venue updated successfully.');
    }
}
