<x-app-layout>
    <x-slot name="title">{{ $roaster->name }} — Coffee Roaster</x-slot>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
        <style>
            #roaster-map { height: 320px; width: 100%; border-radius: 12px; z-index: 0; }
        </style>
    @endpush
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display font-bold text-2xl text-gray-900">{{ $roaster->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $roaster->city }}</p>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('roasters.edit', $roaster) }}"
                           class="px-4 py-2 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:border-indigo-300 hover:text-indigo-600 transition-colors">
                            Edit roaster
                        </a>
                    @endif
                @endauth
                @if($roaster->website)
                    <a href="{{ $roaster->website }}" target="_blank"
                       class="px-4 py-2 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:border-indigo-300 hover:text-indigo-600 transition-colors">
                        Visit website
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- About --}}
            @if($roaster->description)
                <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
                    <h2 class="font-display font-bold text-lg text-gray-900 mb-3">About</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $roaster->description }}</p>
                </div>
            @endif
            <div>
            <h2 class="font-display font-bold text-lg text-gray-900 mb-4">
                Venues serving {{ $roaster->name }} coffee
            </h2>
            </div>

            {{-- Map of venues --}}
            @if($roaster->venues->where('lat', '!=', null)->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-8">
                    <div id="roaster-map"></div>
                </div>
            @endif

            {{-- Venues --}}
            <div>

                @if($roaster->venues->isEmpty())
                    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
                        <p class="text-gray-500 text-sm">
                            No venues linked to this roaster yet.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($roaster->venues as $venue)
                            <a href="{{ route('venues.show', $venue) }}"
                               class="group flex items-center justify-between bg-white rounded-2xl border border-gray-200 p-5
                                      hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                                <div>
                                    <h3 class="font-display font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $venue->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-0.5">
                                        {{ $venue->city }}
                                        @if($venue->address) · {{ $venue->address }} @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $venue->review_count }} {{ Str::plural('review', $venue->review_count) }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($venue->coffee_score > 0)
                                        <div class="text-2xl font-display font-bold text-indigo-600">
                                            {{ number_format($venue->coffee_score, 1) }}
                                        </div>
                                        <div class="text-xs text-gray-400">coffee score</div>
                                    @else
                                        <div class="text-xs text-gray-300">No score<br>yet</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            const venues = @json($roaster->venues->whereNotNull('lat')->whereNotNull('lng')->values());

            if (venues.length > 0) {
                const map = L.map('roaster-map', {
                    scrollWheelZoom: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                }).addTo(map);

                const icon = L.divIcon({
                    className: '',
                    html: `<div style="
                        width: 36px;
                        height: 36px;
                        background: #4f46e5;
                        border: 3px solid white;
                        border-radius: 50%;
                        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 16px;
                    ">☕</div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -20],
                });

                const markers = [];

                venues.forEach(venue => {
                    const marker = L.marker([venue.lat, venue.lng], { icon: icon })
                        .addTo(map)
                        .bindPopup(`
                            <div style="font-family:sans-serif;min-width:160px;">
                                <p style="font-weight:600;font-size:13px;margin:0 0 2px;">${venue.name}</p>
                                <p style="font-size:11px;color:#6b7280;margin:0 0 8px;">${venue.city}</p>
                                ${venue.coffee_score > 0 ? `<p style="font-size:12px;color:#4f46e5;font-weight:700;margin:0 0 6px;">${parseFloat(venue.coffee_score).toFixed(1)} coffee score</p>` : ''}
                                <a href="/venues/${venue.slug}"
                                   style="display:block;text-align:center;background:#4f46e5;color:white;
                                          padding:5px 10px;border-radius:6px;font-size:11px;
                                          text-decoration:none;font-weight:500;">
                                    View venue
                                </a>
                            </div>
                        `);
                    markers.push(marker);
                });

                if (venues.length === 1) {
                    map.setView([parseFloat(venues[0].lat), parseFloat(venues[0].lng)], 13);
                } else {
                    const bounds = L.latLngBounds(markers.map(m => m.getLatLng()));
                    map.fitBounds(bounds, { padding: [40, 40] });
                }
            }
        </script>
    @endpush
</x-app-layout>
