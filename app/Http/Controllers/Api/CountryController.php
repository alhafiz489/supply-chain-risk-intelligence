<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data countries berhasil diambil.',
            'data' => $countries,
        ]);
    }

    public function show($id)
    {
        $country = Country::with(['ports', 'news'])->find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail country berhasil diambil.',
            'data' => $country,
        ]);
    }
}