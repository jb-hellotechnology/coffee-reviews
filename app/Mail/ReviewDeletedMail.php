<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewDeletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $venueName,
        public string $reviewBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your review has been removed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.review-deleted',
            with: [
                'name'        => $this->user->name,
                'venueName'   => $this->venueName,
                'reviewBody'  => $this->reviewBody,
                'indexUrl'    => route('venues.index'),
            ]
        );
    }
}
