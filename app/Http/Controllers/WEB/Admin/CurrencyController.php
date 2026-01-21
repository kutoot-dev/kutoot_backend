<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\World;
use Nnjeim\World\Models\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Currency::orderBy('name', 'asc');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $currencies = $query->paginate(10)->appends($request->all());
        return view('admin.currency', compact('currencies'));
    }

    public function show($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return back()->with('error', 'Currency not found');
        }
        return view('admin.show_currency', compact('currency'));
    }

    public function create()
    {
        return view('admin.create_currency');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
        ]);

        Currency::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
        ]);

        return redirect()->route('admin.currency.index')
            ->with('messege', 'Currency created successfully')
            ->with('alert-type', 'success');
    }

    public function edit($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return back()->with('error', 'Currency not found');
        }
        return view('admin.edit_currency', compact('currency'));
    }

    public function update(Request $request, $id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return back()->with('error', 'Currency not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
        ]);

        $currency->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
        ]);

        return redirect()->route('admin.currency.index')
            ->with('messege', 'Currency updated successfully')
            ->with('alert-type', 'success');
    }

    public function destroy($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return back()->with('error', 'Currency not found');
        }

        $currency->delete();

        return redirect()->route('admin.currency.index')
            ->with('messege', 'Currency deleted successfully')
            ->with('alert-type', 'success');
    }

    // API methods for JSON responses
    public function apiIndex()
    {
        $currencies = Currency::orderBy('name', 'asc')->get();
        return response()->json(['currencies' => $currencies]);
    }

    public function apiShow($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }
        return response()->json(['currency' => $currency]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code',
            'symbol' => 'nullable|string|max:10',
        ]);

        $currency = Currency::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
        ]);

        return response()->json(['message' => 'Currency created successfully', 'currency' => $currency], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code,' . $id,
            'symbol' => 'nullable|string|max:10',
        ]);

        $currency->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'symbol' => $request->symbol,
        ]);

        return response()->json(['message' => 'Currency updated successfully', 'currency' => $currency]);
    }

    public function apiDestroy($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            return response()->json(['message' => 'Currency not found'], 404);
        }

        $currency->delete();
        return response()->json(['message' => 'Currency deleted successfully']);
    }
}
