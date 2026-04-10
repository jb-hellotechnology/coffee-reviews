<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    public function fetchFromPlaceId(string $placeId): ?array
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'fields'   => 'name,formatted_address,geometry,opening_hours,website,formatted_phone_number,address_components',
            'key'      => config('services.google.places_key'),
        ]);

        if ($response->failed() || $response->json('status') !== 'OK') {
            return null;
        }

        $place = $response->json('result');

        return [
            'google_place_id' => $placeId,
            'name'            => $place['name'],
            'address'         => $place['formatted_address'],
            'lat'             => $place['geometry']['location']['lat'],
            'lng'             => $place['geometry']['location']['lng'],
            'phone'           => $place['formatted_phone_number'] ?? null,
            'website'         => $place['website'] ?? null,
            'opening_hours'   => $place['opening_hours']['periods'] ?? null,
            'city'            => $this->extractCity($place['address_components'] ?? []),
            'postcode'        => $this->extractPostcode($place['address_components'] ?? []),
        ];
    }

    public function extractPlaceIdFromUrl(string $url): ?string
    {
        // Step 1: Follow redirects to get the full Google Maps URL
        $resolved = $this->resolveShortUrl($url);

        // Step 2: Try to extract place_id directly from the full URL
        if (preg_match('/place_id=([^&]+)/', $resolved, $matches)) {
            return $matches[1];
        }

        // Step 3: Try to extract the venue name from the URL path
        // Full URLs look like: /maps/place/Venue+Name/@lat,lng,...
        if (preg_match('~/maps/place/([^/@]+)~', $resolved, $matches)) {
            $venueName = urldecode(str_replace('+', ' ', $matches[1]));

            // Also try to extract coordinates to improve accuracy
            $location = null;
            if (preg_match('/@([-\d.]+),([-\d.]+)/', $resolved, $coords)) {
                $location = $coords[1] . ',' . $coords[2];
            }

            return $this->findPlaceIdByName($venueName, $location);
        }

        // Step 4: Last resort — send the full resolved URL as a text query
        return $this->findPlaceIdByName($resolved, null);
    }

    private function resolveShortUrl(string $url): string
    {
        try {
            // Make a request without following redirects to grab the Location header
            $response = Http::withoutRedirecting()->get($url);
            $location = $response->header('Location');

            if ($location) {
                // If it redirects to another short URL, follow one more time
                if (str_contains($location, 'goo.gl') || str_contains($location, 'maps.app')) {
                    $response2 = Http::withoutRedirecting()->get($location);
                    return $response2->header('Location') ?? $location;
                }
                return $location;
            }
        } catch (\Exception $e) {
            // Fall through and return the original URL
        }

        return $url;
    }

    private function findPlaceIdByName(string $query, ?string $location): ?string
    {
        $params = [
            'input'     => $query,
            'inputtype' => 'textquery',
            'fields'    => 'place_id',
            'key'       => config('services.google.places_key'),
        ];

        if ($location) {
            $params['locationbias'] = 'point:' . $location;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', $params);

        return $response->json('candidates.0.place_id');
    }

    private function extractCity(array $components): ?string
    {
        foreach ($components as $component) {
            if (in_array('postal_town', $component['types'])) {
                return $component['long_name'];
            }
            if (in_array('locality', $component['types'])) {
                return $component['long_name'];
            }
        }
        return null;
    }

    private function extractPostcode(array $components): ?string
    {
        foreach ($components as $component) {
            if (in_array('postal_code', $component['types'])) {
                return $component['long_name'];
            }
        }
        return null;
    }
}
