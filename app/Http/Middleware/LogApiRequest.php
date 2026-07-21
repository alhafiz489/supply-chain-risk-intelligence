<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogApiRequest
{
    private const MAX_RESPONSE_BYTES = 20000;

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        $response = $next($request);

        $responseTime = (int) round(
            (microtime(true) - $startedAt) * 1000
        );

        try {
            ApiLog::create([
                'method' => strtoupper($request->method()),
                'endpoint' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => max(0, $responseTime),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_payload' => $this->sanitize(
                    $request->except([
                        'password',
                        'password_confirmation',
                        'token',
                        'api_key',
                        'authorization',
                    ])
                ),
                'response_payload' => $this->extractResponsePayload(
                    $response
                ),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return $response;
    }

    private function extractResponsePayload(Response $response): ?array
    {
        if (! method_exists($response, 'getContent')) {
            return null;
        }

        $content = (string) $response->getContent();

        if ($content === '') {
            return null;
        }

        if (strlen($content) > self::MAX_RESPONSE_BYTES) {
            $decoded = json_decode($content, true);

            return [
                '_truncated' => true,
                '_size_bytes' => strlen($content),
                '_top_level_keys' => is_array($decoded)
                    ? array_slice(array_keys($decoded), 0, 20)
                    : [],
                '_message' => 'Response omitted to protect database storage.',
            ];
        }

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $this->sanitize(
                is_array($decoded)
                    ? $decoded
                    : ['value' => $decoded]
            );
        }

        return [
            'content' => mb_substr($content, 0, 10000),
        ];
    }

    private function sanitize(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'refresh_token',
            'api_key',
            'authorization',
        ];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $data[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }
}
