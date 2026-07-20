<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCache extends Model
{
    protected $fillable = [
        'country_id',
        'external_id',
        'title',
        'category',
        'sentiment',
        'positive_score',
        'negative_score',
        'matched_words',
        'summary',
        'url',
        'image_url',
        'source_name',
        'source_url',
        'language',
        'published_date',
        'published_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'positive_score' => 'integer',
            'negative_score' => 'integer',
            'matched_words' => 'array',
            'published_date' => 'date',
            'published_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}