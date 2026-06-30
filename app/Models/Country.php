<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso2',
        'region',
        'capital',
        'currency_code',
        'latitude',
        'longitude',
        'gdp_usd_billion',
        'inflation_rate',
        'population_million',
        'exchange_rate_to_idr',
        'currency_volatility_percent',
        'weather_condition',
        'temperature',
        'rainfall_mm',
        'wind_speed_kmh',
    ];

    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    public function news()
    {
        return $this->hasMany(NewsCache::class);
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function watchlist()
    {
        return $this->hasOne(Watchlist::class);
    }
}