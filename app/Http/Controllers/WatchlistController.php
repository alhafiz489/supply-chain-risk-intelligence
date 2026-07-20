<?php

namespace App\Http\Controllers;

use App\Models\RiskScore;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * Mengambil daftar negara favorit pengguna yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $watchlists = Watchlist::with('country')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar favorit berhasil diambil.',
            'data' => $watchlists,
        ]);
    }

    /**
     * Menambahkan negara ke daftar favorit.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $latestRisk = RiskScore::where(
            'country_id',
            $validated['country_id']
        )
            ->latest()
            ->first();

        $watchlist = Watchlist::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'country_id' => $validated['country_id'],
            ],
            [
                'notes' => $validated['notes'] ?? null,
                'last_risk_score' => $latestRisk?->total_score,
                'last_risk_label' => $latestRisk?->risk_label,
            ]
        );

        if (! $watchlist->wasRecentlyCreated) {
            return response()->json([
                'success' => false,
                'message' => 'Negara tersebut sudah ada di daftar favorit.',
                'data' => $watchlist->load('country'),
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Negara berhasil ditambahkan ke daftar favorit.',
            'data' => $watchlist->load('country'),
        ], 201);
    }

    /**
     * Menghapus negara dari daftar favorit pengguna.
     */
    public function destroyByCountry(
        Request $request,
        int $countryId
    ): JsonResponse {
        $watchlist = Watchlist::where(
            'user_id',
            $request->user()->id
        )
            ->where('country_id', $countryId)
            ->first();

        if (! $watchlist) {
            return response()->json([
                'success' => false,
                'message' => 'Negara tidak ditemukan di daftar favorit.',
            ], 404);
        }

        $watchlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Negara berhasil dihapus dari daftar favorit.',
        ]);
    }
}