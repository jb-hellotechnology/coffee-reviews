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

        $action = $result['moderation_action'];

        // Auto-reject — unpublish and flag
        if ($action === 'reject' || $result['contains_code_injection']) {
            $this->review->update([
                'verified'          => false,
                'ai_analysed'       => true,
                'moderation_action' => 'reject',
                'moderation_reason' => $result['moderation_reason'] ?? 'Automatically rejected by content moderation.',
                'ai_tags'           => [],
            ]);

            // Delete any uploaded photo
            if ($this->review->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->review->photo);
                $this->review->update(['photo' => null, 'photo_alt' => null]);
            }

            Log::warning('Review auto-rejected', [
                'review_id' => $this->review->id,
                'reason'    => $result['moderation_reason'],
            ]);

            return;
        }

        // Flag for human review
        if ($action === 'flag') {
            $this->review->update([
                'verified'          => false,
                'ai_analysed'       => true,
                'ai_tags'           => $result['tags'],
                'moderation_action' => 'flag',
                'moderation_reason' => $result['moderation_reason'],
            ]);

            Log::info('Review flagged for moderation', [
                'review_id' => $this->review->id,
                'reason'    => $result['moderation_reason'],
            ]);

            return;
        }

        // Approve — write tags and publish
        $this->review->update([
            'ai_tags'           => $result['tags'],
            'ai_analysed'       => true,
            'verified'          => true,
            'moderation_action' => 'approve',
            'moderation_reason' => null,
        ]);

        // Sync extracted tags to pivot table
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
            'moderation_action'  => $action,
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
