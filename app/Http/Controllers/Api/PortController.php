<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $query = Port::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Data ports berhasil diambil.',
            'data' => $query->get(),
        ]);
    }
}