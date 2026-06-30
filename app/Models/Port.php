<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'city',
        'latitude',
        'longitude',
        'congestion_level',
        'delay_days',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}