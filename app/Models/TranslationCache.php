<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslationCache extends Model
{
    protected $table = 'translation_cache';

    protected $fillable = [
        'source_locale',
        'target_locale',
        'source_hash',
        'source_text',
        'translated_text',
        'provider',
        'status',
        'error_message',
        'translated_at',
    ];

    protected function casts(): array
    {
        return [
            'translated_at' => 'datetime',
        ];
    }

    public function scopeSuccessful(
        Builder $query
    ): Builder {
        return $query
            ->where('status', 'success')
            ->whereNotNull('translated_text');
    }
}