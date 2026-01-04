<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ZohoAuthController extends Controller
{
    /**
     * Step 1: Redirect to Zoho consent screen
     */
    public function redirectToZoho()
    {
        $query = http_build_query([
            'scope'         => 'ZohoInventory.FullAccess.all',
            'client_id'     => config('services.zoho.client_id'),
            'response_type' => 'code',
            'redirect_uri'  => config('services.zoho.redirect_uri'),
            'access_type'   => 'offline',
            'prompt'        => 'consent',
        ]);

        return redirect(config('services.zoho.accounts_url') . '/oauth/v2/auth?' . $query);
    }

    /**
     * Step 2: Handle Zoho callback & save refresh token
     */
    public function handleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return response()->json(['error' => 'Authorization code not received'], 400);
        }

        $response = Http::asForm()->post(
            config('services.zoho.accounts_url') . '/oauth/v2/token',
            [
                'grant_type'    => 'authorization_code',
                'client_id'     => config('services.zoho.client_id'),
                'client_secret' => config('services.zoho.client_secret'),
                'redirect_uri'  => config('services.zoho.redirect_uri'),
                'code'          => $request->code,
            ]
        );

        $data = $response->json();
        if (!isset($data['refresh_token'])) {
            return response()->json([
                'error' => 'Failed to get refresh token',
                'zoho_response' => $data
            ], 400);
        }

        // Save / Update token in DB
        DB::table('zoho_tokens')->updateOrInsert(
            ['id' => 1],
            [
                'refresh_token' => $data['refresh_token'],
                'access_token'  => $data['access_token'],
                'expires_at'    => now(),
                'updated_at'    => now(),
                'created_at'    => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Zoho refresh token generated and saved successfully',
        ]);
    }
}
