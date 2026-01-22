<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\World;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\Country;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(Request $request)
    {
        $query = City::with('state.country');

        if ($request->has('state_id') && $request->state_id) {
            $query->where('state_id', $request->state_id);
        }

        $cities = $query->orderBy('name', 'asc')->get();
        return response()->json(['cities' => $cities]);
    }

    public function show($id)
    {
        $city = City::with('state.country')->find($id);
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }
        return response()->json(['city' => $city]);
    }

    public function create()
    {
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        $states = State::orderBy('name', 'asc')->get();
        return response()->json(['countries' => $countries, 'states' => $states]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
            'country_code' => 'nullable|string|max:10',
        ]);

        $city = City::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
            'country_code' => $request->country_code,
        ]);

        return response()->json(['message' => 'City created successfully', 'city' => $city], 201);
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
            'country_code' => 'nullable|string|max:10',
        ]);

        $city->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
            'country_code' => $request->country_code,
        ]);

        return response()->json(['message' => 'City updated successfully', 'city' => $city]);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $city->delete();
        return response()->json(['message' => 'City deleted successfully']);
    }
}
