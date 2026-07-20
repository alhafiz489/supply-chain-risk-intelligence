<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],
            'sentiment' => [
                'nullable',
                Rule::in([
                    'Positive',
                    'Neutral',
                    'Negative',
                ]),
            ],
            'category' => [
                'nullable',
                'string',
                'max:50',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $query = NewsCache::query()
            ->with([
                'country:id,name,iso2,iso3',
            ])
            ->latest('published_at')
            ->latest('id');

        if (isset($validated['country_id'])) {
            $query->where(
                'country_id',
                $validated['country_id']
            );
        }

        if (isset($validated['sentiment'])) {
            $query->where(
                'sentiment',
                $validated['sentiment']
            );
        }

        if (isset($validated['category'])) {
            $query->where(
                'category',
                $validated['category']
            );
        }

        if (isset($validated['search'])) {
            $search = $validated['search'];

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where(
                        'title',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'summary',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'source_name',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $perPage = (int) (
            $validated['per_page'] ?? 50
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Data berita global berhasil diambil.',
            'data' => $query->paginate($perPage),
        ]);
    }
}