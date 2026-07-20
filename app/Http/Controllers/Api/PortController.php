<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PortController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'source' => [
                'nullable',
                'string',
                'max:80',
            ],
            'status_code' => [
                'nullable',
                'string',
                'max:2',
            ],
            'has_coordinates' => [
                'nullable',
                Rule::in(['0', '1', 0, 1, true, false]),
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $query = Port::query()
            ->select([
                'id',
                'country_id',
                'unlocode',
                'location_code',
                'name',
                'city',
                'status_code',
                'function_code',
                'latitude',
                'longitude',
                'congestion_level',
                'delay_days',
                'source',
                'source_version',
                'data_status',
                'is_reference_active',
                'synced_at',
            ])
            ->with([
                'country:id,name,iso2,iso3',
            ])
            ->orderBy('name');

        if (isset($validated['country_id'])) {
            $query->where(
                'country_id',
                $validated['country_id']
            );
        }

        if (isset($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'city',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'unlocode',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if (isset($validated['source'])) {
            $query->where(
                'source',
                $validated['source']
            );
        }

        if (isset($validated['status_code'])) {
            $query->where(
                'status_code',
                strtoupper($validated['status_code'])
            );
        }

        if (
            array_key_exists(
                'has_coordinates',
                $validated
            )
        ) {
            $hasCoordinates = filter_var(
                $validated['has_coordinates'],
                FILTER_VALIDATE_BOOLEAN
            );

            if ($hasCoordinates) {
                $query
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude');
            } else {
                $query->where(function ($builder) {
                    $builder
                        ->whereNull('latitude')
                        ->orWhereNull('longitude');
                });
            }
        }

        $perPage = (int) (
            $validated['per_page'] ?? 25
        );

        $paginator = $query
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' =>
                'Data pelabuhan berhasil diambil.',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' =>
                    $paginator->currentPage(),
                'last_page' =>
                    $paginator->lastPage(),
                'per_page' =>
                    $paginator->perPage(),
                'total' =>
                    $paginator->total(),
                'from' =>
                    $paginator->firstItem(),
                'to' =>
                    $paginator->lastItem(),
            ],
            'links' => [
                'first' =>
                    $paginator->url(1),
                'last' =>
                    $paginator->url(
                        $paginator->lastPage()
                    ),
                'previous' =>
                    $paginator->previousPageUrl(),
                'next' =>
                    $paginator->nextPageUrl(),
            ],
        ]);
    }
}