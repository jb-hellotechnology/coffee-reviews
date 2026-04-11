<?php

namespace App\Http\Controllers;

use App\Models\Roaster;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoasterController extends Controller
{
    public function index(): View
    {
        $roasters = Roaster::withCount('venues')
            ->orderBy('name')
            ->paginate(24);

        return view('roasters.index', compact('roasters'));
    }

    public function show(Roaster $roaster): View
    {
        $roaster->load(['venues' => function ($q) {
            $q->orderByDesc('coffee_score');
        }]);

        return view('roasters.show', compact('roaster'));
    }

    public function create(): View
    {
        return view('roasters.create');
    }

    public function edit(Roaster $roaster): View
    {
        return view('roasters.edit', compact('roaster'));
    }

    public function update(Request $request, Roaster $roaster): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'website'     => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $roaster->update([
            'name'        => $request->name,
            'city'        => $request->city,
            'website'     => $request->website,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('roasters.show', $roaster)
            ->with('success', 'Roaster updated successfully.');
    }
}
