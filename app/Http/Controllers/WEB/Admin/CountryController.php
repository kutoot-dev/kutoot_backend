<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\World;
use Nnjeim\World\Models\Country;

/**
 * @group Country
 */
class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Country::orderBy('name', 'asc');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $countries = $query->paginate(10)->appends($request->all());
        return view('admin.country', compact('countries'));
    }

    public function show($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return back()->with('error', 'Country not found');
        }
        return view('admin.show_country', compact('country'));
    }

    public function create()
    {
        return view('admin.create_country');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iso2' => 'nullable|string|max:2|unique:countries,iso2',
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        Country::create([
            'name' => $request->name,
            'iso2' => $request->iso2 ? strtoupper($request->iso2) : strtoupper(substr($request->name, 0, 2)),
            'iso3' => $request->iso3 ? strtoupper($request->iso3) : strtoupper(substr($request->name, 0, 3)),
            'phone_code' => $request->phone_code ?? '',
            'status' => $request->status,
        ]);

        return redirect()->route('admin.country.index')
            ->with(['messege' => 'Country created successfully', 'alert-type' => 'success', 'message' => 'Country created successfully']);
    }

    public function edit($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return back()->with('error', 'Country not found');
        }
        return view('admin.edit_country', compact('country'));
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return back()->with('error', 'Country not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'iso2' => 'nullable|string|max:2|unique:countries,iso2,' . $id,
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        $country->update([
            'name' => $request->name,
            'iso2' => $request->iso2 ? strtoupper($request->iso2) : $country->iso2,
            'iso3' => $request->iso3 ? strtoupper($request->iso3) : $country->iso3,
            'phone_code' => $request->phone_code ?? '',
            'status' => $request->status,
        ]);

        return redirect()->route('admin.country.index')
            ->with(['messege' => 'Country updated successfully', 'alert-type' => 'success', 'message' => 'Country updated successfully']);
    }

    public function destroy($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return back()->with('error', 'Country not found');
        }

        $country->delete();

        return redirect()->route('admin.country.index')
            ->with(['messege' => 'Country deleted successfully', 'alert-type' => 'success']);
    }

    public function changeStatus($id)
    {
        $country = Country::find($id);
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

    // API methods
    public function apiIndex()
    {
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        return response()->json(['countries' => $countries]);
    }
}
