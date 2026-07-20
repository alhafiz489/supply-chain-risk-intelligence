<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApiLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $method = strtoupper(
            trim((string) $request->query('method'))
        );
        $statusGroup = trim(
            (string) $request->query('status_group')
        );
        $dateFrom = trim(
            (string) $request->query('date_from')
        );
        $dateTo = trim(
            (string) $request->query('date_to')
        );

        if (! in_array(
            $method,
            ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
            true
        )) {
            $method = '';
        }

        if (! in_array(
            $statusGroup,
            ['success', 'client_error', 'server_error'],
            true
        )) {
            $statusGroup = '';
        }

        $logs = ApiLog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($logQuery) use ($search) {
                    $logQuery
                        ->where('endpoint', 'like', "%{$search}%")
                        ->orWhere('route_name', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->when($method !== '', function ($query) use ($method) {
                $query->where('method', $method);
            })
            ->when(
                $statusGroup === 'success',
                fn ($query) => $query->whereBetween(
                    'status_code',
                    [200, 399]
                )
            )
            ->when(
                $statusGroup === 'client_error',
                fn ($query) => $query->whereBetween(
                    'status_code',
                    [400, 499]
                )
            )
            ->when(
                $statusGroup === 'server_error',
                fn ($query) => $query->whereBetween(
                    'status_code',
                    [500, 599]
                )
            )
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'total' => ApiLog::count(),
            'success' => ApiLog::whereBetween(
                'status_code',
                [200, 399]
            )->count(),
            'client_error' => ApiLog::whereBetween(
                'status_code',
                [400, 499]
            )->count(),
            'server_error' => ApiLog::whereBetween(
                'status_code',
                [500, 599]
            )->count(),
        ];

        return view(
            'admin.api-logs.index',
            compact(
                'logs',
                'statistics',
                'search',
                'method',
                'statusGroup',
                'dateFrom',
                'dateTo'
            )
        );
    }

    public function show(ApiLog $apiLog): View
    {
        return view(
            'admin.api-logs.show',
            compact('apiLog')
        );
    }
}