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

        // 🔁 Bootstrap from .env if DB empty
        if (!$token && config('services.zoho.refresh_token')) {
            DB::table('zoho_tokens')->insert([
                'refresh_token' => config('services.zoho.refresh_token'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $token = DB::table('zoho_tokens')->first();
        }

        if (!$token) {
            throw new \Exception('Zoho refresh token not found in DB or .env');
        }

        if (!empty($token->access_token) && now()->lt($token->expires_at)) {
            return $token->access_token;
        }

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
            $error = $data['error'] ?? 'Unknown error';

            Log::error('Zoho OAuth failed', [
                'status'   => $response->status(),
                'response' => $data,
            ]);

            // Clear invalid tokens and provide actionable guidance
            if (in_array($error, ['invalid_code', 'invalid_client', 'access_denied'])) {
                DB::table('zoho_tokens')->truncate();

                throw new \Exception(
                    "Zoho OAuth failed: {$error}. Refresh token is invalid or revoked. " .
                    "Please re-authenticate by visiting: " . url('/zoho/connect')
                );
            }

            throw new \Exception('Zoho OAuth failed: ' . $error);
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
