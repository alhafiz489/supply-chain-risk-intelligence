<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentWord extends Model
{
    protected $fillable = [
        'word',
        'type',
        'weight',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}