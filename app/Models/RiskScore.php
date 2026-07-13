<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}