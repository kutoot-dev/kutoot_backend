<?php

namespace App\Services\Zoho;

use App\Models\User;

class ZohoCustomerService
{
    protected ZohoClient $zoho;

    public function __construct(ZohoClient $zoho)
    {
        $this->zoho = $zoho;
    }

    /**
     * Get or create Zoho customer and return customer_id
     */
    public function getOrCreate(User $user): string
    {
        if (!empty($user->zoho_customer_id)) {
            return $user->zoho_customer_id;
        }

        $payload = [
            'contact_name' => $user->name,
            'customer_name'=> $user->name,
            'email'        => $user->email,
            'contact_type' => 'customer',
        ];

        $response = $this->zoho->post('/customers', $payload);

        if (!isset($response['customer']['customer_id'])) {
            throw new \Exception('Failed to create Zoho customer');
        }

        $user->update([
            'zoho_customer_id' => $response['customer']['customer_id'],
        ]);

        return $response['customer']['customer_id'];
    }
}
