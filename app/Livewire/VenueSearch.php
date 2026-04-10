<?php

namespace App\Livewire;

use App\Models\Venue;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

class VenueSearch extends Component
{
    use WithPagination;

    public string $query = '';
    public string $city = '';
    public string $sortBy = 'score';

    protected $queryString = [
        'query'  => ['except' => ''],
        'city'   => ['except' => ''],
        'sortBy' => ['except' => 'score'],
    ];

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatingCity(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $cities = Venue::select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $venues = Venue::query()
            ->when($this->city, fn($q) => $q->where('city', $this->city))
            ->when(trim($this->query) !== '', function ($q) {
                $search = '%' . $this->query . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'LIKE', $search)
                          ->orWhere('city', 'LIKE', $search)
                          ->orWhere('address', 'LIKE', $search)
                          ->orWhereHas('reviews', function ($r) use ($search) {
                              $r->whereRaw(
                                  "JSON_SEARCH(ai_tags, 'one', ?) IS NOT NULL",
                                  [$this->query]
                              );
                          });
                });
            })
            ->when(
                $this->sortBy === 'recent',
                fn($q) => $q->orderByDesc('created_at'),
                fn($q) => match($this->sortBy) {
                    'most'  => $q->orderByDesc('review_count'),
                    default => $q->orderByDesc('coffee_score'),
                }
            )
            ->paginate(18);

        return view('livewire.venue-search', compact('venues', 'cities'));
    }
}
