<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    protected $fillable = [
        'name',
        'official_name',
        'iso2',
        'iso3',
        'region',
        'subregion',
        'capital',
        'currency_code',
        'primary_language_code',
        'primary_language_name',
        'primary_language_native_name',
        'text_direction',
        'latitude',
        'longitude',
        'gdp_usd_billion',
        'gdp_data_year',
        'inflation_rate',
        'inflation_data_year',
        'population_million',
        'population',
        'population_data_year',
        'is_sovereign',
        'is_un_member',
        'is_dependency',
        'flag_url',
        'exchange_rate_to_idr',
        'currency_volatility_percent',
        'currency_rate_date',
        'currency_source',
        'currency_data_status',
        'weather_condition',
        'temperature',
        'rainfall_mm',
        'wind_speed_kmh',
        'data_completeness_percent',
        'risk_data_status',
        'master_synced_at',
        'weather_synced_at',
        'economic_synced_at',
        'currency_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'gdp_usd_billion' => 'decimal:2',
            'gdp_data_year' => 'integer',
            'inflation_rate' => 'decimal:2',
            'inflation_data_year' => 'integer',
            'population_million' => 'decimal:2',
            'population' => 'integer',
            'population_data_year' => 'integer',
            'is_sovereign' => 'boolean',
            'is_un_member' => 'boolean',
            'is_dependency' => 'boolean',
            'exchange_rate_to_idr' => 'decimal:2',
            'currency_volatility_percent' => 'decimal:2',
            'currency_rate_date' => 'date',
            'temperature' => 'decimal:2',
            'rainfall_mm' => 'decimal:2',
            'wind_speed_kmh' => 'decimal:2',
            'data_completeness_percent' => 'integer',
            'master_synced_at' => 'datetime',
            'weather_synced_at' => 'datetime',
            'economic_synced_at' => 'datetime',
            'currency_synced_at' => 'datetime',
        ];
    }

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(NewsCache::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function watchlist(): HasOne
    {
        return $this->hasOne(Watchlist::class);
    }
}