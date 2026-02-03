<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\Country;

/**
 * @group City
 */
class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = City::with(['state', 'country'])->orderBy('name', 'asc');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        if ($request->has('country_id') && $request->country_id != '') {
            $query->where('country_id', $request->country_id);
        }
        if ($request->has('state_id') && $request->state_id != '') {
            $query->where('state_id', $request->state_id);
        }

        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        $states = collect();
        if ($request->country_id) {
            $states = State::where('country_id', $request->country_id)->orderBy('name', 'asc')->get();
        }

        $cities = $query->paginate(10)->appends($request->all());
        return view('admin.city', compact('cities', 'countries', 'states'));
    }

    public function create()
    {
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.create_city', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|exists:states,id',
            'country' => 'required|exists:countries,id',
            'country_code' => 'nullable|string|max:10',
        ]);

        City::create([
            'name' => $request->name,
            'state_id' => $request->state,
            'country_id' => $request->country,
            'country_code' => $request->country_code,
        ]);

        return redirect()->route('admin.city.index')
            ->with(['messege' => 'City created successfully', 'alert-type' => 'success', 'message' => 'City created successfully']);
    }

    public function edit($id)
    {
        $city = City::find($id);
        if (!$city) {
            return back()->with('error', 'City not found');
        }
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        $states = State::where('country_id', $city->country_id)->orderBy('name', 'asc')->get();
        return view('admin.edit_city', compact('city', 'countries', 'states'));
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            return back()->with('error', 'City not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|exists:states,id',
            'country' => 'required|exists:countries,id',
            'country_code' => 'nullable|string|max:10',
        ]);

        $city->update([
            'name' => $request->name,
            'state_id' => $request->state,
            'country_id' => $request->country,
            'country_code' => $request->country_code,
        ]);

        return redirect()->route('admin.city.index')
            ->with(['messege' => 'City updated successfully', 'alert-type' => 'success', 'message' => 'City updated successfully']);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return back()->with('error', 'City not found');
        }

        $city->delete();

        return redirect()->route('admin.city.index')
            ->with(['messege' => 'City deleted successfully', 'alert-type' => 'success']);
    }
}
