<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZohoBooksService
{
    private function getAccessToken()
    {
        $response = Http::asForm()->post('https://accounts.zoho.in/oauth/v2/token', [
            'grant_type'    => 'refresh_token',
            'client_id'     => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'refresh_token' => config('services.zoho.refresh_token'),
        ]);

        return $response->json()['access_token'];
    }

    public function request($method, $endpoint, $params = [])
    {
        $accessToken = $this->getAccessToken();

        return Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->$method(
            config('services.zoho.api_domain') . $endpoint,
            array_merge($params, [
                'organization_id' => config('services.zoho.org_id'),
            ])
        )->json();
    }
}
