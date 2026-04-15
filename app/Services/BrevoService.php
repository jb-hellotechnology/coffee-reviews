<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    private string $baseUrl = 'https://api.brevo.com/v3';

    private function headers(): array
    {
        return [
            'api-key'      => config('services.brevo.key'),
            'content-type' => 'application/json',
            'accept'       => 'application/json',
        ];
    }

    public function addContact(string $email, string $name, bool $doubleOptIn = false): bool
    {
        $listId = (int) config('services.brevo.list_id');

        $payload = [
            'email'          => $email,
            'attributes'     => ['FIRSTNAME' => $name],
            'listIds'        => [$listId],
            'updateEnabled'  => true,
        ];

        // Use double opt-in if requested
        if ($doubleOptIn) {
            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/contacts/doubleOptinConfirmation", array_merge($payload, [
                    'templateId'     => 1, // Your Brevo double opt-in template ID
                    'redirectionUrl' => config('app.url'),
                ]));
        } else {
            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/contacts", $payload);
        }

        if ($response->failed()) {
            // 400 with code "duplicate_parameter" means contact already exists — not an error
            if ($response->status() === 400 && str_contains($response->body(), 'duplicate_parameter')) {
                return true;
            }

            Log::error('Brevo API error', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'email'   => $email,
            ]);

            return false;
        }

        return true;
    }

    public function removeContact(string $email): bool
    {
        $listId = (int) config('services.brevo.list_id');

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/contacts/lists/{$listId}/contacts/remove", [
                'emails' => [$email],
            ]);

        return $response->successful();
    }
}
