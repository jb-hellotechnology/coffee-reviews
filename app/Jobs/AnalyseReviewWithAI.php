<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\Tag;
use App\Services\AI\ReviewAnalyserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalyseReviewWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public Review $review) {}

    public function handle(ReviewAnalyserService $analyser): void
    {
        Log::info('Analysing review', ['review_id' => $this->review->id]);

        $result = $analyser->analyse($this->review->body);

        // Write results back to the review
        $this->review->update([
            'ai_tags'     => $result['tags'],
            'ai_analysed' => true,
        ]);

        // Sync extracted tags to the pivot table
        if (!empty($result['tags'])) {
            $tagIds = collect($result['tags'])->map(function (string $tagName) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName, 'category' => 'ai-extracted']
                )->id;
            });

            $this->review->tags()->syncWithoutDetaching($tagIds);
        }

        Log::info('Review analysis complete', [
            'review_id'          => $this->review->id,
            'tags'               => $result['tags'],
            'coffee_focus_score' => $result['coffee_focus_score'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Review analysis job failed', [
            'review_id' => $this->review->id,
            'error'     => $exception->getMessage(),
        ]);
    }
}
