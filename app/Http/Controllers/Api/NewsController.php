<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsCache::with('country')->latest();

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data news berhasil diambil.',
            'data' => $query->get(),
        ]);
    }
}