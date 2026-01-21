<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\World;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\Country;

class CountryStateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index(Request $request)
    {
        $query = State::with('country');

        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        $states = $query->orderBy('name', 'asc')->get();
        return response()->json(['states' => $states]);
    }

    public function show($id)
    {
        $state = State::with('country')->find($id);
        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }
        return response()->json(['state' => $state]);
    }

    public function create()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return response()->json(['countries' => $countries]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $state = State::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
        ]);

        return response()->json(['message' => 'State created successfully', 'state' => $state], 201);
    }

    public function update(Request $request, $id)
    {
        $state = State::find($id);
        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
        ]);

        $state->update([
            'name' => $request->name,
            'country_id' => $request->country_id,
        ]);

        return response()->json(['message' => 'State updated successfully', 'state' => $state]);
    }

    public function destroy($id)
    {
        $state = State::find($id);
        if (!$state) {
            return response()->json(['message' => 'State not found'], 404);
        }

        $state->delete();
        return response()->json(['message' => 'State deleted successfully']);
    }
}
