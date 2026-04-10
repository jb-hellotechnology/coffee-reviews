<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicClient
{
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function complete(string $prompt): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post($this->baseUrl, [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 512,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

            if ($response->failed()) {
                Log::error('Anthropic API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('content.0.text');

        } catch (\Exception $e) {
            Log::error('Anthropic client exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
