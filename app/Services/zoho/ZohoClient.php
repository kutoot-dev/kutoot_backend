<?php

namespace App\Services\zoho;

use Illuminate\Support\Facades\Http;

class ZohoClient
{
    protected ZohoTokenService $tokenService;

    public function __construct(ZohoTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Zoho-oauthtoken ' . $this->tokenService->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];
    }

    protected function baseUrl(string $endpoint): string
    {
        return config('zoho.base_url') . $endpoint
            . '?organization_id=' . config('zoho.org_id');
    }

    public function get(string $endpoint): array
    {
        return Http::withHeaders($this->headers())
            ->get($this->baseUrl($endpoint))
            ->json();
    }

    public function post(string $endpoint, array $data = []): array
    {
        return Http::withHeaders($this->headers())
            ->post($this->baseUrl($endpoint), $data)
            ->json();
    }
}
