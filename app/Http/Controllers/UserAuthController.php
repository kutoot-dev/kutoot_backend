<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => Auth::guard('api')->user()
        ]);
    }

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}
