<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'method',
        'endpoint',
        'route_name',
        'status_code',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'request_payload',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'request_payload' => 'array',
            'response_payload' => 'array',
        ];
    }
}