<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\World;
use Nnjeim\World\Models\Country;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->paginate(10);
        return response()->json(['countries' => $countries]);
    }

    public function show($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }
        return response()->json(['country' => $country]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iso2' => 'required|string|max:2|unique:countries,iso2',
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'iso2' => strtoupper($request->iso2),
            'iso3' => $request->iso3 ? strtoupper($request->iso3) : null,
            'phone_code' => $request->phone_code,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Country created successfully', 'country' => $country], 201);
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'iso2' => 'required|string|max:2|unique:countries,iso2,' . $id,
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $country->update([
            'name' => $request->name,
            'iso2' => strtoupper($request->iso2),
            'iso3' => $request->iso3 ? strtoupper($request->iso3) : null,
            'phone_code' => $request->phone_code,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Country updated successfully', 'country' => $country]);
    }

    public function destroy($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $country->delete();
        return response()->json(['message' => 'Country deleted successfully']);
    }

    public function changeStatus($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        if ($country->status == 1) {
            $country->status = 0;
            $country->save();
            $message = trans('admin_validation.Inactive Successfully');
        } else {
            $country->status = 1;
            $country->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
