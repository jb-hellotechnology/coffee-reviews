<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ReviewScore extends Model
{
    use HasUuids;

    protected $fillable = [
        'review_id',
        'espresso',
        'milk_work',
        'filter_options',
        'bean_sourcing',
        'barista_knowledge',
        'equipment',
        'decaf_available',
        'value',
        'overall',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
