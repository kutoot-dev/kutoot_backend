<?php

namespace App\Http\Controllers\WEB\Store\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StoreLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:store')->except('logout');
    }

    public function showLoginForm()
    {
        return view('store.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find seller by username
        $seller = Seller::where('username', $request->username)->first();

        // Check if seller exists and password matches
        if (!$seller || !Hash::check($request->password, $seller->password)) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials'])->withInput();
        }

        // Check if seller is active
        if (!$seller->status) {
            return redirect()->back()->withErrors(['username' => 'Your account is inactive. Please contact support.'])->withInput();
        }

        // Check if seller has approved store application
        if (!$seller->hasApprovedApplication()) {
            return redirect()->back()->withErrors(['username' => 'Your store application is not approved yet. Please wait for approval.'])->withInput();
        }

        // Manually log in the seller
        Auth::guard('store')->login($seller);

        return redirect()->route('store.dashboard');
    }

    public function logout()
    {
        Auth::guard('store')->logout();
        return redirect()->route('store.login');
    }
}


