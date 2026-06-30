<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $fillable = [
        'country_id',
        'title',
        'category',
        'sentiment',
        'summary',
        'published_date',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}