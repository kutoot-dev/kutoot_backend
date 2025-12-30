<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ShiprocketService
{
    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.shiprocket.base_url'), '/');
        // ❌ DO NOT authenticate here
    }

    /**
     * Authenticate & store token (LAZY AUTH)
     */
    protected function authenticate(): void
    {
        if (!empty($this->token)) {
            return;
        }

        $response = Http::post($this->baseUrl . '/auth/login', [
            'email'    => config('services.shiprocket.email'),
            'password' => config('services.shiprocket.password'),
        ]);

        if (!$response->successful()) {
            Log::error('Shiprocket auth failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            throw new Exception('Shiprocket authentication failed');
        }

        $this->token = $response->json('token');

        if (empty($this->token)) {
            throw new Exception('Shiprocket token missing in response');
        }
    }

    /**
     * GET request
     */
    protected function get(string $endpoint, array $query = []): array
    {
        $this->authenticate();

        return Http::withToken($this->token)
            ->get($this->baseUrl . $endpoint, $query)
            ->json();
    }

    /**
     * POST request
     */
    protected function post(string $endpoint, array $payload = []): array
    {
        $this->authenticate();

        return Http::withToken($this->token)
            ->post($this->baseUrl . $endpoint, $payload)
            ->json();
    }

    /**
     * Check pincode serviceability
     */
    public function checkPincodeServiceability(
        int $pickupPincode,
        int $deliveryPincode,
        bool $isCod = false,
        float $weight = 0.5
    ): bool {
        $response = $this->get('/courier/serviceability/', [
            'pickup_postcode'   => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,
            'cod'               => $isCod ? 1 : 0,
            'weight'            => $weight,
        ]);
        Log::info('Shiprocket pincode check', [
            'pickup'   => $pickupPincode,
            'delivery' => $deliveryPincode,
            'response' => $response,
        ]);

        return !empty($response['data']['available_courier_companies']);
    }

    /**
     * Courier serviceability (raw)
     */
    public function checkCourier(array $payload): array
    {
        return $this->get('/courier/serviceability/', $payload);
    }

    /**
     * Create shipment
     */
    public function createOrder(array $payload): array
    {
        $response = $this->post('/orders/create/adhoc', $payload);

        Log::info('Shiprocket create order response', $response);

        if (
            isset($response['status_code']) &&
            (int) $response['status_code'] === 1 &&
            isset($response['shipment_id'])
        ) {
            return $response;
        }

        throw new Exception($response['message'] ?? 'Shiprocket order creation failed');
    }

    /**
     * Generate AWB
     */
    public function generateAwb(int $shipmentId): ?string
    {
        $response = $this->post('/courier/assign/awb', [
            'shipment_id' => $shipmentId,
        ]);

        Log::info('Shiprocket AWB response', $response);

        return $response['awb_code'] ?? null;
    }

    /**
     * Track shipment
     */
    public function track(string $awb): array
    {
        return $this->post('/courier/track/awb', [
            'awb_code' => $awb,
        ]);
    }
}
