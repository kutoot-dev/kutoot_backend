<?php

namespace App\Http\Controllers\WEB\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nnjeim\World\Models\Currency;

/** @group Currency
 
 * CurrencyController - Displays currencies from the World package.
 * @group Currency
 
 * This is a read-only view of currencies from the nnjeim/world package.
 * Currencies are seeded via `php artisan world:install`.
 */
class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $query = Currency::with('country')->orderBy('name', 'asc');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('code', 'LIKE', "%{$request->search}%");
        }

        $currencies = $query->paginate(10)->appends($request->all());
        return view('admin.currency', compact('currencies'));
    }

    public function show($id)
    {
        $currency = Currency::with('country')->find($id);
        if (!$currency) {
            return back()->with('error', 'Currency not found');
        }
        return view('admin.show_currency', compact('currency'));
    }

    // Note: Create, store, edit, update, destroy methods removed
    // World package currencies are read-only reference data
    public function create()
    {
        return redirect()->route('admin.currency.index')
            ->with('error', 'World currencies are read-only');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.currency.index')
            ->with('error', 'World currencies are read-only');
    }

    public function edit($id)
    {
        return redirect()->route('admin.currency.index')
            ->with('error', 'World currencies are read-only');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.currency.index')
            ->with('error', 'World currencies are read-only');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.currency.index')
            ->with('error', 'World currencies are read-only');
    }
}
