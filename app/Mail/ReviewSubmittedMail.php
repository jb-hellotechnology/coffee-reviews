<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your review has been submitted',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.review-submitted',
            with: [
                'name'      => $this->review->user->name,
                'venue'     => $this->review->venue->name,
                'venueUrl'  => route('venues.show', $this->review->venue),
                'body'      => $this->review->body,
            ]
        );
    }
}
