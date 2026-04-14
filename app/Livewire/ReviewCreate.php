<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\ReviewScore;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Component;
use App\Mail\ReviewSubmittedMail;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ReviewCreate extends Component
{
    public Venue $venue;

    use WithFileUploads;

    public $photo = null;

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
        'photo'             => 'nullable|image|max:20480', // 20MB
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
        // Fast synchronous injection check before any DB writes
        if ($this->containsInjection($this->body)) {
            $this->addError('body', 'Your review contains invalid content and cannot be submitted.');
            return;
        }

        $this->validate();

        if (!Review::userCanReviewVenue(auth()->user(), $this->venue)) {
            $this->addError('body', 'You have already reviewed this venue in the last 3 months.');
            return;
        }

        $photoPath = null;
        if ($this->photo) {
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $image = $manager->decodePath($this->photo->getRealPath());

            // Only resize down if wider than 2000px
            if ($image->width() > 2000) {
                $image->scale(width: 2000);
            }

            $encoded = $image->encode(new \Intervention\Image\Encoders\JpegEncoder(quality: 70));

            $filename = 'reviews/' . uniqid() . '.jpg';

            Storage::disk('public')->put($filename, (string) $encoded);

            $photoPath = $filename;
        }

        $review = Review::create([
            'user_id'  => auth()->id(),
            'venue_id' => $this->venue->id,
            'body'     => $this->body,
            'photo'    => $photoPath,
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

    private function containsInjection(string $text): bool
    {
        $patterns = [
            '/<[^>]*>/',                          // HTML tags
            '/javascript:/i',                      // JS protocol
            '/on\w+\s*=/i',                        // Event handlers
            '/SELECT\s+.*\s+FROM/i',               // SQL SELECT
            '/INSERT\s+INTO/i',                    // SQL INSERT
            '/DROP\s+TABLE/i',                     // SQL DROP
            '/UNION\s+SELECT/i',                   // SQL UNION
            '/\{\{.*\}\}/',                        // Template injection
            '/\$\{.*\}/',                          // JS template literals
            '/<\?php/i',                           // PHP tags
            '/eval\s*\(/i',                        // eval()
            '/base64_decode\s*\(/i',               // base64 decode
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    public function render(): View
    {
        return view('livewire.review-create');
    }
}
