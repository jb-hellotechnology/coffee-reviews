<?php

namespace App\Mail;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VenueVerifiedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Venue $venue,
    ) {}

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.venue-verified',
            with: [
                'name'      => $this->user->name,
                'venueName' => $this->venue->name,
                'venueUrl'  => route('venues.show', $this->venue),
            ]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->venue->name} has been verified",
        );
    }
}
