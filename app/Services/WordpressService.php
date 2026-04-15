<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.wordpress.api_url');
    }

    public function getPosts(int $perPage = 10, int $page = 1): array
    {
        $cacheKey = "wp_posts_{$perPage}_{$page}";

        return Cache::remember($cacheKey, 300, function () use ($perPage, $page) {
            $response = Http::withBasicAuth(
                config('services.wordpress.user'),
                config('services.wordpress.password')
            )->get("{$this->apiUrl}/posts", [
                'per_page' => $perPage,
                'page'     => $page,
                '_embed'   => true,
            ]);

            if ($response->failed()) {
                Log::error('WordPress API error', ['status' => $response->status()]);
                return ['posts' => [], 'total' => 0, 'pages' => 0];
            }

            return [
                'posts' => $response->json(),
                'total' => (int) $response->header('X-WP-Total'),
                'pages' => (int) $response->header('X-WP-TotalPages'),
            ];
        });
    }

    public function getPost(string $slug): ?array
    {
        $cacheKey = "wp_post_{$slug}";

        return Cache::remember($cacheKey, 300, function () use ($slug) {
            $response = Http::withBasicAuth(
                config('services.wordpress.user'),
                config('services.wordpress.password')
            )->get("{$this->apiUrl}/posts", [
                'slug'   => $slug,
                '_embed' => true,
            ]);

            if ($response->failed() || empty($response->json())) {
                return null;
            }

            return $response->json()[0] ?? null;
        });
    }

    public function getCategories(): array
    {
        return Cache::remember('wp_categories', 3600, function () {
            $response = Http::withBasicAuth(
                config('services.wordpress.user'),
                config('services.wordpress.password')
            )->get("{$this->apiUrl}/categories");

            return $response->successful() ? $response->json() : [];
        });
    }

    public function getFeaturedImageUrl(array $post): ?string
    {
        return $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
    }

    public function getAuthorName(array $post): string
    {
        return $post['_embedded']['author'][0]['name'] ?? 'Coffee Shop Reviews';
    }

    public function getCategoryNames(array $post): array
    {
        $terms = $post['_embedded']['wp:term'] ?? [];
        foreach ($terms as $termGroup) {
            foreach ($termGroup as $term) {
                if ($term['taxonomy'] === 'category' && $term['name'] !== 'Uncategorized') {
                    $categories[] = $term['name'];
                }
            }
        }
        return $categories ?? [];
    }
}
