<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Liste des pays disponibles.
     */
    public function countries(): JsonResponse
    {
        $countries = Country::orderBy('name')->get(['id', 'name', 'code']);

        return response()->json($countries);
    }

    /**
     * Recherche ou liste de villes par pays avec autocomplétion tolérante aux accents.
     */
    public function cities(Request $request): JsonResponse
    {
        $query = City::query()->with('country:id,name,code');

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->integer('country_id'));
        } elseif ($request->filled('country_code')) {
            $code = strtoupper($request->string('country_code')->trim());
            $query->whereHas('country', fn ($q) => $q->where('code', $code));
        }

        if ($request->filled('q')) {
            $term = City::normalizeString($request->string('q')->trim());
            $query->where('normalized_name', 'like', "%{$term}%");
        }

        $cities = $query->orderBy('name')
            ->limit(30)
            ->get(['id', 'country_id', 'name']);

        return response()->json($cities);
    }
}
