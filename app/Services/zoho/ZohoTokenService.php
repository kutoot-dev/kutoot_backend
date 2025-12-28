<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoTokenService
{
    /**
     * Get valid access token (auto-refresh if needed)
     */
    public function getAccessToken(): string
    {
        $token = DB::table('zoho_tokens')->first();

        if (!$token) {
            throw new \Exception('Zoho refresh token not found in database');
        }

        // ✅ Return token ONLY if it exists and is not expired
        if (!empty($token->access_token) && now()->lt($token->expires_at)) {
            return $token->access_token;
        }

        // 🔄 Otherwise generate a new one
        return $this->refreshToken($token->refresh_token);
    }

    /**
     * Generate access token using refresh_token
     */
    protected function refreshToken(string $refreshToken): string
    {
        $response = Http::asForm()->post(
            config('services.zoho.accounts_url') . '/oauth/v2/token',
            [
                'refresh_token' => $refreshToken,
                'client_id'     => config('services.zoho.client_id'),
                'client_secret' => config('services.zoho.client_secret'),
                'grant_type'    => 'refresh_token',
            ]
        );

        $data = $response->json();

        // 🔴 IMPORTANT: Handle OAuth failure properly
        if (!$response->successful() || !isset($data['access_token'])) {
            Log::error('Zoho OAuth failed', [
                'status'   => $response->status(),
                'response' => $data,
            ]);

            throw new \Exception(
                'Zoho OAuth failed: ' . ($data['error'] ?? 'Unknown error')
            );
        }

        // ✅ Save new token
        DB::table('zoho_tokens')->update([
            'access_token' => $data['access_token'],
            'expires_at'   => now()->addSeconds($data['expires_in']),
            'updated_at'   => now(),
        ]);

        return $data['access_token'];
    }
}
