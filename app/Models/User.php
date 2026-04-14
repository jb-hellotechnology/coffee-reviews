<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'bio',
        'website',
        'avatar',
        'expertise_level',
        'is_coffee_expert',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function avatarUrl(): ?string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        return null;
    }

    public static function expertiseLevels(): array
    {
        return [
            'drinker'      => ['emoji' => '☕', 'label' => 'Coffee drinker',      'description' => 'I know what I like'],
            'curious'      => ['emoji' => '🫘', 'label' => 'Coffee curious',      'description' => 'I\'m starting to care about beans'],
            'enthusiast'   => ['emoji' => '🔧', 'label' => 'Coffee enthusiast',   'description' => 'I own a grinder at home'],
            'geek'         => ['emoji' => '📚', 'label' => 'Coffee geek',         'description' => 'I\'ve done a barista course'],
            'professional' => ['emoji' => '🏆', 'label' => 'Coffee professional', 'description' => 'I work in specialty coffee'],
        ];
    }

    public function expertiseLabel(): ?string
    {
        if (!$this->expertise_level) return null;
        $levels = self::expertiseLevels();
        return $levels[$this->expertise_level]['emoji'] . ' ' . $levels[$this->expertise_level]['label'] ?? null;
    }

    public function isTopReviewer(): bool
    {
        return $this->reviews()->where('verified', true)->count() >= 10;
    }

    public function isCoffeeExpert(): bool
    {
        return (bool) $this->is_coffee_expert;
    }
}
