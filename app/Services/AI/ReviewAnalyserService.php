<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class ReviewAnalyserService
{
    public function __construct(private AnthropicClient $client) {}

    public function analyse(string $reviewText): array
    {
        $prompt = <<<PROMPT
You are analysing a coffee shop review for a specialist coffee platform.
Your job is to extract structured coffee-specific information from the review.

Review text:
"{$reviewText}"

Respond with a JSON object only — no preamble, no markdown, no code fences.
Use exactly this structure:

{
  "tags": [],
  "mentions_espresso": false,
  "mentions_filter": false,
  "mentions_bean_origin": false,
  "mentions_equipment": false,
  "coffee_focus_score": 0.0,
  "prompt_for_more": false,
  "prompt_message": null
}

Rules:
- "tags" should be specific coffee terms only. Good examples: "single origin", "Ethiopian Yirgacheffe", "La Marzocca", "AeroPress", "natural process", "latte art", "flat white", "oat milk". Bad examples: "good coffee", "nice place", "friendly staff".
- "coffee_focus_score" is a number from 0 to 1 representing how much the review focuses specifically on coffee quality rather than general cafe experience. 1.0 = entirely about coffee, 0.0 = no mention of coffee at all.
- Set "prompt_for_more" to true if coffee_focus_score is below 0.4.
- If "prompt_for_more" is true, set "prompt_message" to a short friendly message encouraging the reviewer to add more coffee-specific detail.
- Set the "mentions_*" booleans based on whether the review explicitly mentions those things.
- Return an empty tags array if no specific coffee terms are found — do not invent tags.
PROMPT;

        $response = $this->client->complete($prompt);

        if (!$response) {
            return $this->emptyResult();
        }

        try {
            // Strip markdown code fences if the model included them
            $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $response);
            $cleaned = preg_replace('/^```\s*$/m', '', $cleaned);
            $cleaned = trim($cleaned);

            $result = json_decode($cleaned, true, 512, JSON_THROW_ON_ERROR);
            return $this->validateResult($result);
        } catch (\JsonException $e) {
            Log::error('Failed to parse AI review analysis', [
                'response' => $response,
                'error'    => $e->getMessage(),
            ]);
            return $this->emptyResult();
        }
    }

    private function validateResult(array $result): array
    {
        return [
            'tags'                 => array_values(array_filter(
                                        (array) ($result['tags'] ?? []),
                                        fn($t) => is_string($t) && strlen($t) > 0
                                      )),
            'mentions_espresso'    => (bool) ($result['mentions_espresso'] ?? false),
            'mentions_filter'      => (bool) ($result['mentions_filter'] ?? false),
            'mentions_bean_origin' => (bool) ($result['mentions_bean_origin'] ?? false),
            'mentions_equipment'   => (bool) ($result['mentions_equipment'] ?? false),
            'coffee_focus_score'   => min(1.0, max(0.0, (float) ($result['coffee_focus_score'] ?? 0))),
            'prompt_for_more'      => (bool) ($result['prompt_for_more'] ?? false),
            'prompt_message'       => $result['prompt_message'] ?? null,
        ];
    }

    private function emptyResult(): array
    {
        return [
            'tags'                 => [],
            'mentions_espresso'    => false,
            'mentions_filter'      => false,
            'mentions_bean_origin' => false,
            'mentions_equipment'   => false,
            'coffee_focus_score'   => 0.0,
            'prompt_for_more'      => false,
            'prompt_message'       => null,
        ];
    }
}
