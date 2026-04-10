<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\ReviewScore;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Component;
use App\Mail\ReviewSubmittedMail;
use Illuminate\Support\Facades\Mail;

class ReviewCreate extends Component
{
    public Venue $venue;

    public string $body = '';

    // Scores — null means not rated
    public ?int $espresso = null;
    public ?int $milk_work = null;
    public ?int $filter_options = null;
    public ?int $bean_sourcing = null;
    public ?int $barista_knowledge = null;
    public ?int $equipment = null;
    public ?int $decaf_available = null;
    public ?int $value = null;

    public bool $saved = false;

    protected array $rules = [
        'body'              => 'required|string|min:30|max:2000',
        'espresso'          => 'nullable|integer|min:1|max:5',
        'milk_work'         => 'nullable|integer|min:1|max:5',
        'filter_options'    => 'nullable|integer|min:1|max:5',
        'bean_sourcing'     => 'nullable|integer|min:1|max:5',
        'barista_knowledge' => 'nullable|integer|min:1|max:5',
        'equipment'         => 'nullable|integer|min:1|max:5',
        'decaf_available'   => 'nullable|integer|min:1|max:5',
        'value'             => 'nullable|integer|min:1|max:5',
    ];

    protected array $messages = [
        'body.required' => 'Please write something about the coffee.',
        'body.min'      => 'Your review needs to be at least 30 characters.',
    ];

    public function mount(Venue $venue): void
    {
        $this->venue = $venue;
    }

    public function setScore(string $field, int $score): void
    {
        // Clicking the same score again clears it
        if ($this->$field === $score) {
            $this->$field = null;
        } else {
            $this->$field = $score;
        }
    }

    public function save(): void
    {
        $this->validate();

        if (!Review::userCanReviewVenue(auth()->user(), $this->venue)) {
            $this->addError('body', 'You have already reviewed this venue in the last 3 months.');
            return;
        }

        $review = Review::create([
            'user_id'  => auth()->id(),
            'venue_id' => $this->venue->id,
            'body'     => $this->body,
        ]);

        ReviewScore::create([
            'review_id'         => $review->id,
            'espresso'          => $this->espresso,
            'milk_work'         => $this->milk_work,
            'filter_options'    => $this->filter_options,
            'bean_sourcing'     => $this->bean_sourcing,
            'barista_knowledge' => $this->barista_knowledge,
            'equipment'         => $this->equipment,
            'decaf_available'   => $this->decaf_available,
            'value'             => $this->value,
        ]);

        $this->saved = true;
        Mail::to(auth()->user())->send(new ReviewSubmittedMail($review));
    }

    public function render(): View
    {
        return view('livewire.review-create');
    }
}
