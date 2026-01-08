<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoClient
{
    protected ZohoTokenService $tokenService;

    public function __construct(ZohoTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Build Zoho authorization headers
     */
    protected function headers(): array
    { 
        $token = $this->tokenService->getAccessToken();

    Log::info('Zoho token used', [
        'token_prefix' => substr($token, 0, 25),
    ]);

            return [
            'Authorization' => 'Zoho-oauthtoken ' . $this->tokenService->getAccessToken(),
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Build full Zoho Books API URL
     */
    protected function url(string $endpoint): string
    {
           
        $baseUrl = rtrim(config('services.zoho.books_base_url'), '/');
        $endpoint = '/' . ltrim($endpoint, '/');

        return $baseUrl
            . $endpoint
            . '?organization_id=' . config('services.zoho.organization_id');
    }

    /**
     * POST request to Zoho Books
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->url($endpoint);

        Log::info('Zoho Books POST', [
            'url' => $url,
            'payload' => $data,
        ]);
        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->post($url, $data);
        Log::info('Zoho Books response', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);  
             
        if ($response->failed()) {
            Log::error('Zoho Books API Error', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception(
                'Zoho API error: ' . $response->body()
            );
        }

        return $response->json();
    }

    
    /**
     * GET request to Zoho Books
     */
    public function get(string $endpoint, array $query = []): array
    {
        $url = $this->url($endpoint);

        Log::info('Zoho Books GET', [
            'url' => $url,
            'query' => $query,
        ]);

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->get($url, $query);

        if ($response->failed()) {
            Log::error('Zoho Books API Error', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception(
                'Zoho API error: ' . $response->body()
            );
        }

        return $response->json();
    }
}