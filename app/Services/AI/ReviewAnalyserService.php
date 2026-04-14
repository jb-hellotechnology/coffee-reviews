<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class ReviewAnalyserService
{
    public function __construct(private AnthropicClient $client) {}

    public function analyse(string $reviewText): array
    {
        $prompt = <<<PROMPT
    You are a content moderator and data extractor for a specialist coffee shop review platform.
    Analyse the following review text and respond with JSON only — no preamble, no markdown, no code fences.

    Review text:
    "{$reviewText}"

    Return exactly this structure:
    {
      "tags": [],
      "mentions_espresso": false,
      "mentions_filter": false,
      "mentions_bean_origin": false,
      "mentions_equipment": false,
      "coffee_focus_score": 0.0,
      "prompt_for_more": false,
      "prompt_message": null,
      "is_spam": false,
      "is_appropriate": true,
      "is_coffee_related": true,
      "contains_code_injection": false,
      "contains_inappropriate_language": false,
      "moderation_reason": null,
      "moderation_action": "approve"
    }

    Rules:
    - "tags" should be specific coffee terms only. Examples: "single origin", "Ethiopian Yirgacheffe", "La Marzocca", "AeroPress", "natural process", "latte art". Never generic terms like "good coffee" or "nice place".
    - "coffee_focus_score" is 0.0–1.0 representing how specifically the review is about coffee quality rather than general cafe experience.
    - Set "prompt_for_more" to true if coffee_focus_score is below 0.4.
    - Set "is_spam" to true if the review appears to be spam, a fake review, a promotional post, or contains repeated characters or nonsensical content.
    - Set "is_appropriate" to false if the review contains profanity, hate speech, threats, sexual content, or personally abusive language directed at staff.
    - Set "is_coffee_related" to false if the review makes no meaningful reference to coffee, drinks, or the cafe experience.
    - Set "contains_code_injection" to true if the review contains HTML tags, SQL statements, JavaScript, template syntax, or any attempt to inject code. Examples: <script>, SELECT *, {{7*7}}, javascript:, DROP TABLE.
    - Set "contains_inappropriate_language" to true if the review contains profanity or offensive language.
    - "moderation_reason" should briefly explain any issue found, or null if none.
    - "moderation_action" should be one of: "approve", "flag", "reject".
      - "approve" — review is fine, publish it.
      - "flag" — review has minor issues, needs human review (e.g. borderline language, slightly off-topic).
      - "reject" — review should be automatically rejected (spam, code injection, seriously inappropriate content).
    PROMPT;

        $response = $this->client->complete($prompt);

        if (!$response) {
            return $this->emptyResult();
        }

        try {
            $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $response);
            $cleaned = preg_replace('/^```\s*$/m', '', $cleaned);
            $result  = json_decode(trim($cleaned), true, 512, JSON_THROW_ON_ERROR);
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
            'tags'                          => array_values(array_filter(
                                                (array) ($result['tags'] ?? []),
                                                fn($t) => is_string($t) && strlen($t) > 0
                                              )),
            'mentions_espresso'             => (bool) ($result['mentions_espresso'] ?? false),
            'mentions_filter'               => (bool) ($result['mentions_filter'] ?? false),
            'mentions_bean_origin'          => (bool) ($result['mentions_bean_origin'] ?? false),
            'mentions_equipment'            => (bool) ($result['mentions_equipment'] ?? false),
            'coffee_focus_score'            => min(1.0, max(0.0, (float) ($result['coffee_focus_score'] ?? 0))),
            'prompt_for_more'               => (bool) ($result['prompt_for_more'] ?? false),
            'prompt_message'                => $result['prompt_message'] ?? null,
            'is_spam'                       => (bool) ($result['is_spam'] ?? false),
            'is_appropriate'                => (bool) ($result['is_appropriate'] ?? true),
            'is_coffee_related'             => (bool) ($result['is_coffee_related'] ?? true),
            'contains_code_injection'       => (bool) ($result['contains_code_injection'] ?? false),
            'contains_inappropriate_language' => (bool) ($result['contains_inappropriate_language'] ?? false),
            'moderation_reason'             => $result['moderation_reason'] ?? null,
            'moderation_action'             => in_array($result['moderation_action'] ?? 'approve', ['approve', 'flag', 'reject'])
                                                ? $result['moderation_action']
                                                : 'approve',
        ];
    }

    private function emptyResult(): array
    {
        return [
            'tags'                          => [],
            'mentions_espresso'             => false,
            'mentions_filter'               => false,
            'mentions_bean_origin'          => false,
            'mentions_equipment'            => false,
            'coffee_focus_score'            => 0.0,
            'prompt_for_more'               => false,
            'prompt_message'                => null,
            'is_spam'                       => false,
            'is_appropriate'                => true,
            'is_coffee_related'             => true,
            'contains_code_injection'       => false,
            'contains_inappropriate_language' => false,
            'moderation_reason'             => null,
            'moderation_action'             => 'approve',
        ];
    }
}
