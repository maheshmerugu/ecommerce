<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\State;
use App\Models\City;
use App\Models\Pincode;

class LocationController extends Controller
{
    public function states()
    {
        $q = request()->query('q');

        // Prefer DB values when tables exist
        if (Schema::hasTable('states')) {
            $query = State::orderBy('name');
            if ($q) $query->where('name', 'like', "%{$q}%");
            $states = $query->limit(200)->pluck('name');
            return response()->json(['states' => $states]);
        }

        $states = array_keys(config('locations.states', []));
        if ($q) {
            $states = array_values(array_filter($states, function ($s) use ($q) {
                return stripos($s, $q) !== false;
            }));
        }

        return response()->json(['states' => $states]);
    }

    public function cities(Request $request)
    {
        $stateName = $request->query('state');
        $q = $request->query('q'); // optional search

        if (Schema::hasTable('cities')) {
            $query = City::query()->with('state');

            if ($stateName) {
                $state = State::where('name', $stateName)->first();
                if ($state) $query->where('state_id', $state->id);
            }

            if ($q) {
                $query->where('name', 'like', "%{$q}%");
            }

            $cities = $query->orderBy('name')->limit(200)->pluck('name');
            return response()->json(['cities' => $cities]);
        }

        $all = config('locations.states', []);

        if ($stateName && isset($all[$stateName])) {
            return response()->json(['cities' => $all[$stateName]]);
        }

        // fallback: flattened list
        $cities = [];
        foreach ($all as $list) {
            $cities = array_merge($cities, $list);
        }
        $cities = array_values(array_unique($cities));

        return response()->json(['cities' => $cities]);
    }

    public function pincodes(Request $request)
    {
        $cityName = $request->query('city');
        $q = $request->query('q');

        if (Schema::hasTable('pincodes')) {
            $query = Pincode::query()->with('city');
            if ($cityName) {
                $city = City::where('name', $cityName)->first();
                if ($city) $query->where('city_id', $city->id);
            }
            if ($q) $query->where('pincode', 'like', "%{$q}%");

            $pincodes = $query->orderBy('pincode')->limit(200)->pluck('pincode');
            return response()->json(['pincodes' => $pincodes]);
        }

        return response()->json(['pincodes' => []]);
    }
}
