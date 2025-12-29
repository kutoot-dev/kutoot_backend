<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ShiprocketService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.shiprocket.base_url');
        $this->token   = $this->authenticate();
    }

    /**
     * Authenticate & get token
     */
    protected function authenticate()
    {
        $response = Http::post($this->baseUrl . '/auth/login', [
            'email'    => config('services.shiprocket.email'),
            'password' => config('services.shiprocket.password'),
        ]);

        if (!$response->successful()) {
            Log::error('Shiprocket auth failed', $response->json());
            throw new Exception('Shiprocket authentication failed');
        }

        return $response->json()['token'];
    }

    /**
     * Common POST request method
     */
    protected function post($endpoint, array $payload = [])
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . $endpoint, $payload);

        return $response->json();
    }

    // ShiprocketService.php

protected function get($endpoint, array $query = [])
{
    $response = Http::withToken($this->token)
        ->get($this->baseUrl . $endpoint, $query);

    return $response->json();
}

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

        \Log::info('Shiprocket pincode check', $response);

        // ✅ Serviceable ONLY if courier list exists
        return !empty($response['data']['available_courier_companies']);
    }
public function checkCourier(array $payload)
{
    return $this->get('/courier/serviceability/', $payload);
}

    /**
     *  Create shipment / order
     */
    public function createOrder(array $payload)
{
    $response = Http::withToken($this->token)
        ->post($this->baseUrl . '/orders/create/adhoc', $payload)
        ->json();

    \Log::info('Shiprocket create order response', $response);

    // ✅ SUCCESS CONDITION (VERY IMPORTANT)
    if (
        isset($response['status_code']) &&
        (int) $response['status_code'] === 1 &&
        isset($response['shipment_id'])
    ) {
        return $response; // ORDER CREATED
    }

    // REAL FAILURE
    throw new \Exception($response['message'] ?? 'Shiprocket order creation failed');
}

    /**
     * 🏷 Generate AWB
     */public function generateAwb($shipmentId)
{
    $response = Http::withToken($this->token)
        ->post($this->baseUrl . '/courier/assign/awb', [
            'shipment_id' => $shipmentId
        ])
        ->json();
    \Log::info('Shiprocket AWB response', $response);

    if (isset($response['awb_code'])) {
        return $response;
    }

    return null; //  AWB not generated yet (NOT ERROR)
}


    /**
     *  Track shipment
     */
    public function track($awb)
    {
        return $this->post('/courier/track/awb', [
            'awb_code' => $awb
        ]);
    }
}
