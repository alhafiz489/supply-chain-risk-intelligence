<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Port extends Model
{
    protected $fillable = [
        'country_id',
        'unlocode',
        'location_code',
        'name',
        'name_without_diacritics',
        'city',
        'subdivision_code',
        'change_indicator',
        'status_code',
        'function_code',
        'iata_code',
        'latitude',
        'longitude',
        'congestion_level',
        'delay_days',
        'source',
        'source_version',
        'source_url',
        'data_status',
        'is_reference_active',
        'remarks',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'delay_days' => 'integer',
            'is_reference_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}