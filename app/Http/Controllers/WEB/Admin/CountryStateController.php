<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\Country;

/**
 * @group Country State
 */
class CountryStateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = State::with('country')->orderBy('name', 'asc');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $states = $query->paginate(10)->appends($request->all());
        return view('admin.state', compact('states'));
    }

    public function create()
    {
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.create_state', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|exists:countries,id',
            'iso2' => 'nullable|string|max:10',
        ]);

        $data = [
            'name' => $request->name,
            'country_id' => $request->country,
        ];
        if (\Schema::hasColumn('states', 'iso2')) {
            $data['iso2'] = $request->iso2 ?? '';
        }
        State::create($data);

        return redirect()->route('admin.state.index')
            ->with(['messege' => 'State created successfully', 'alert-type' => 'success', 'message' => 'State created successfully']);
    }

    public function edit($id)
    {
        $state = State::find($id);
        if (!$state) {
            return back()->with('error', 'State not found');
        }
        $countries = Country::where('status', 1)->orderBy('name', 'asc')->get();
        return view('admin.edit_state', compact('state', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $state = State::find($id);
        if (!$state) {
            return back()->with('error', 'State not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|exists:countries,id',
            'iso2' => 'nullable|string|max:10',
        ]);

        $data = [
            'name' => $request->name,
            'country_id' => $request->country,
        ];
        if (\Schema::hasColumn('states', 'iso2')) {
            $data['iso2'] = $request->iso2 ?? '';
        }
        $state->update($data);

        return redirect()->route('admin.state.index')
            ->with(['messege' => 'State updated successfully', 'alert-type' => 'success', 'message' => 'State updated successfully']);
    }

    public function destroy($id)
    {
        $state = State::find($id);
        if (!$state) {
            return back()->with('error', 'State not found');
        }

        $state->delete();

        return redirect()->route('admin.state.index')
            ->with(['messege' => 'State deleted successfully', 'alert-type' => 'success']);
    }
}
