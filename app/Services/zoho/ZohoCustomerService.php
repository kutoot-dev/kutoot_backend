<?php

namespace App\Services\Zoho;

use App\Models\User;

class ZohoCustomerService
{
    protected ZohoClient $zoho;

   public function __construct(ZohoClient $zoho)
    { 
        $this->zoho = $zoho;
        //$this->zoho = $zoho;
    }

    /**
     * Get or create Zoho customer and return customer_id
     */
    public function getOrCreate(User $user): string
    { 
        // print_r($user); 
        if (!empty($user->zoho_customer_id)) {
            return $user->zoho_customer_id;
        }

        $payload = [
            'contact_name' => $user->name,
            'email'        => $user->email,
            'contact_type' => 'customer',
        ];
        $response = $this->zoho->post('/contacts', $payload);
        // print_r($response);
        if (!isset($response['contact']['contact_id'])) {
            throw new \Exception('Failed to create Zoho customer');
        }
        
        $user->zoho_customer_id = $response['contact']['contact_id'];
        $user->save();

        return $response['contact']['contact_id'];
    }
}