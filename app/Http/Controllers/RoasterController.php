<?php

namespace App\Http\Controllers;

use App\Models\Roaster;
use Illuminate\View\View;

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
}
