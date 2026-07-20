<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'weather_risk',
        'inflation_risk',
        'currency_risk',
        'news_risk',
        'port_risk',
        'total_score',
        'risk_label',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'weather_risk' => 'integer',
            'inflation_risk' => 'integer',
            'currency_risk' => 'integer',
            'news_risk' => 'integer',
            'port_risk' => 'integer',
            'total_score' => 'integer',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}