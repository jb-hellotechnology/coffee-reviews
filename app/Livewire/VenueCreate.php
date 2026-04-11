<?php

namespace App\Livewire;

use App\Models\Venue;
use App\Services\GooglePlacesService;
use Illuminate\Support\Str;
use Livewire\Component;

class VenueCreate extends Component
{
    // Google Maps URL field
    public string $mapsUrl = '';
    public bool $lookupLoading = false;
    public ?string $lookupError = null;

    // Venue fields
    public string $name = '';
    public string $address = '';
    public string $city = '';
    public string $postcode = '';
    public string $phone = '';
    public string $website = '';
    public ?float $lat = null;
    public ?float $lng = null;
    public ?string $googlePlaceId = null;
    public ?string $roasterId = null;

    // State
    public bool $formVisible = false;
    public bool $saved = false;

    protected array $rules = [
        'name'     => 'required|string|max:255',
        'address'  => 'required|string|max:255',
        'city'     => 'required|string|max:100',
        'postcode' => 'nullable|string|max:20',
        'phone'    => 'nullable|string|max:30',
        'website'  => 'nullable|url|max:255',
        'lat'      => 'required|numeric',
        'lng'      => 'required|numeric',
        'roasterId' => 'nullable|exists:roasters,id',
    ];

    public function lookupFromUrl(): void
    {
        $this->lookupError = null;
        $this->lookupLoading = true;

        $service = app(GooglePlacesService::class);

        $placeId = $service->extractPlaceIdFromUrl($this->mapsUrl);

        if (!$placeId) {
            $this->lookupError = 'Could not find a venue from that URL. Try pasting a different Google Maps link, or fill in the details manually.';
            $this->lookupLoading = false;
            $this->formVisible = true;
            return;
        }

        $data = $service->fetchFromPlaceId($placeId);

        if (!$data) {
            $this->lookupError = 'Found the place but could not retrieve its details. Please fill in manually.';
            $this->lookupLoading = false;
            $this->formVisible = true;
            return;
        }

        // Populate form fields
        $this->googlePlaceId = $data['google_place_id'];
        $this->name          = $data['name'];
        $this->address       = $data['address'];
        $this->city          = $data['city'] ?? '';
        $this->postcode      = $data['postcode'] ?? '';
        $this->phone         = $data['phone'] ?? '';
        $this->website       = $data['website'] ?? '';
        $this->lat           = $data['lat'];
        $this->lng           = $data['lng'];

        $this->lookupLoading = false;
        $this->formVisible = true;
    }

    public function skipLookup(): void
    {
        $this->formVisible = true;
    }

    public function save(): void
    {
        $this->validate();

        // Prevent duplicate Google Place entries
        if ($this->googlePlaceId && Venue::where('google_place_id', $this->googlePlaceId)->exists()) {
            $this->addError('name', 'This venue has already been added to the platform.');
            return;
        }

        Venue::create([
            'google_place_id' => $this->googlePlaceId,
            'suggested_by'    => auth()->id(),
            'name'            => $this->name,
            'slug'            => Str::slug($this->name) . '-' . Str::random(4),
            'address'         => $this->address,
            'city'            => $this->city,
            'postcode'        => $this->postcode,
            'phone'           => $this->phone,
            'website'         => $this->website,
            'lat'             => $this->lat,
            'lng'             => $this->lng,
            'roaster_id'      => $this->roasterId ?: null,
        ]);

        $this->saved = true;
    }

    public function render()
    {
        $roasters = \App\Models\Roaster::orderBy('name')->get(['id', 'name']);
        return view('livewire.venue-create', compact('roasters'));
    }
}
