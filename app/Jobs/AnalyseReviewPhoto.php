<?php

namespace App\Jobs;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnalyseReviewPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public Review $review) {}

    public function handle(): void
    {
        if (!$this->review->photo) {
            return;
        }

        $photoPath = Storage::disk('public')->path($this->review->photo);

        if (!file_exists($photoPath)) {
            Log::error('Review photo not found', ['review_id' => $this->review->id]);
            return;
        }

        $imageData = base64_encode(file_get_contents($photoPath));
        $mimeType  = mime_content_type($photoPath);

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 256,
            'messages'   => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type'   => 'image',
                            'source' => [
                                'type'       => 'base64',
                                'media_type' => $mimeType,
                                'data'       => $imageData,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'You are moderating photos submitted with coffee shop reviews. Analyse this image and respond with JSON only — no preamble, no markdown.

Return exactly this structure:
{
  "is_appropriate": true,
  "is_coffee_related": true,
  "alt_text": "A flat white with latte art in a white ceramic cup on a wooden table",
  "reason": null
}

Rules:
- "is_appropriate" should be false if the image contains nudity, violence, gore, offensive content, or anything unrelated to a coffee shop visit.
- "is_coffee_related" should be true if the image shows coffee, drinks, food, the interior or exterior of a cafe, or anything you would expect to see in a coffee shop review.
- "alt_text" should be a concise, descriptive alt tag (under 125 characters) suitable for the image as used in a coffee review context.
- "reason" should explain why the image is inappropriate if is_appropriate is false, otherwise null.',
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Photo analysis API error', [
                'review_id' => $this->review->id,
                'status'    => $response->status(),
            ]);
            return;
        }

        $text = $response->json('content.0.text');

        try {
            $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $text);
            $cleaned = preg_replace('/^```\s*$/m', '', $cleaned);
            $result  = json_decode(trim($cleaned), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::error('Failed to parse photo analysis', [
                'review_id' => $this->review->id,
                'response'  => $text,
            ]);
            return;
        }

        if (!($result['is_appropriate'] ?? true) || !($result['is_coffee_related'] ?? true)) {
            // Delete the photo and clear the field
            Storage::disk('public')->delete($this->review->photo);

            $this->review->update([
                'photo'          => null,
                'photo_alt'      => null,
                'photo_analysed' => true,
            ]);

            Log::info('Review photo deleted — inappropriate or not coffee related', [
                'review_id'        => $this->review->id,
                'is_appropriate'   => $result['is_appropriate'] ?? true,
                'is_coffee_related' => $result['is_coffee_related'] ?? true,
                'reason'           => $result['reason'] ?? null,
            ]);

            return;
        }

        $this->review->update([
            'photo_alt'      => $result['alt_text'] ?? null,
            'photo_analysed' => true,
        ]);

        Log::info('Review photo analysed', [
            'review_id'        => $this->review->id,
            'alt_text'         => $result['alt_text'] ?? null,
            'is_coffee_related' => $result['is_coffee_related'] ?? null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Review photo analysis job failed', [
            'review_id' => $this->review->id,
            'error'     => $exception->getMessage(),
        ]);
    }
}
