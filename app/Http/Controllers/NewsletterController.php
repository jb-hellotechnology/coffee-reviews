<?php

namespace App\Http\Controllers;

use App\Services\BrevoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:255',
        ]);

        $success = app(BrevoService::class)->addContact(
            $request->email,
            $request->name ?? 'Coffee fan',
        );

        if ($success) {
            return back()->with('newsletter_success', 'You\'re subscribed! Thanks for joining.');
        }

        return back()->with('newsletter_error', 'Something went wrong. Please try again.');
    }
}
