<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'notes',
        'last_risk_score',
        'last_risk_label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}