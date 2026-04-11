<x-app-layout>
    <x-slot name="title">Map</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Map</h2>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css"/>
        <style>
            #map { height: calc(100vh - 120px); width: 100%; }
        </style>
    @endpush

    <div style="position: relative;">
        <div id="map"></div>

        {{-- Legend --}}
        <div style="position: absolute; bottom: 32px; left: 16px; z-index: 1000;
                    background: white; border-radius: 8px; padding: 8px 12px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15); border: 1px solid #e5e7eb;">
            <p style="font-size: 12px; font-weight: 600; color: #111827; margin: 0 0 4px;">
                {{ $venues->count() }} {{ Str::plural('venue', $venues->count()) }}
            </p>
            <p style="font-size: 11px; font-weight: 500; color: #374151; margin: 0 0 4px;">Coffee score</p>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; color: #6b7280;">
                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#f87171;"></span> 1–2
                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#fbbf24;"></span> 2–3
                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#fde047;"></span> 3–4
                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#4ade80;"></span> 4–5
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.min.js"></script>
        <script>
            const venues = @json($venues);

            function markerColour(score) {
                if (score >= 4) return '#4ade80';
                if (score >= 3) return '#fde047';
                if (score >= 2) return '#fbbf24';
                return '#f87171';
            }

            function makeIcon(score) {
                const colour = markerColour(score);
                return L.divIcon({
                    className: '',
                    html: `<div style="
                        width: 36px;
                        height: 36px;
                        background: ${colour};
                        border: 2px solid white;
                        border-radius: 50%;
                        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 11px;
                        font-weight: 600;
                        color: #1f2937;
                        cursor: pointer;
                    ">${score > 0 ? score.toFixed(1) : '–'}</div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -20],
                });
            }

            // Initialise map
            const map = L.map('map').setView([54.0, -2.0], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(map);

            // Marker cluster group
            const markerCluster = L.markerClusterGroup({
                maxClusterRadius: 60,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                iconCreateFunction: function(cluster) {
                    const count = cluster.getChildCount();
                    return L.divIcon({
                        className: '',
                        html: `<div style="
                            width: 40px;
                            height: 40px;
                            background: #4f46e5;
                            border: 2px solid white;
                            border-radius: 50%;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 13px;
                            font-weight: 700;
                            color: white;
                            cursor: pointer;
                        ">${count}</div>`,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                    });
                }
            });

            // Plot all venue markers
            venues.forEach(venue => {
                const score = parseFloat(venue.coffee_score) || 0;

                const popup = `
                    <div style="min-width: 180px; font-family: sans-serif;">
                        <p style="font-weight: 600; font-size: 14px; margin: 0 0 2px;">${venue.name}</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 0 0 8px;">${venue.city}</p>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 12px; color: #6b7280;">
                                ${venue.review_count} ${venue.review_count === 1 ? 'review' : 'reviews'}
                            </span>
                            <span style="font-size: 18px; font-weight: 700; color: #4f46e5;">
                                ${score > 0 ? score.toFixed(1) : '–'}
                            </span>
                        </div>
                        <a href="/venues/${venue.slug}"
                           style="display: block; text-align: center; background: #4f46e5; color: white;
                                  padding: 6px 12px; border-radius: 6px; font-size: 12px;
                                  text-decoration: none; font-weight: 500;">
                            View venue
                        </a>
                    </div>
                `;

                const marker = L.marker([venue.lat, venue.lng], { icon: makeIcon(score) });
                marker.bindPopup(popup, { maxWidth: 220 });
                markerCluster.addLayer(marker);
            });

            map.addLayer(markerCluster);

            // User location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.setView([lat, lng], 11);
                    },
                    function() {
                        // Denied or failed — fit to all markers
                        if (venues.length > 1) {
                            map.fitBounds(L.latLngBounds(venues.map(v => [v.lat, v.lng])), { padding: [40, 40] });
                        } else if (venues.length === 1) {
                            map.setView([parseFloat(venues[0].lat), parseFloat(venues[0].lng)], 14);
                        }
                    },
                    { timeout: 8000, maximumAge: 300000 }
                );
            } else {
                if (venues.length > 1) {
                    map.fitBounds(L.latLngBounds(venues.map(v => [v.lat, v.lng])), { padding: [40, 40] });
                } else if (venues.length === 1) {
                    map.setView([parseFloat(venues[0].lat), parseFloat(venues[0].lng)], 14);
                }
            }
        </script>
    @endpush

</x-app-layout>
