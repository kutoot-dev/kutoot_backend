<?php

namespace App\Services;

class ZohoService
{
    protected $zoho;

    public function __construct(\App\Services\Zoho\ZohoClient $zoho)
    {
        $this->zoho = $zoho;
    }

    /**
     * Create item in Zoho Inventory
     */
    public function createItem(array $data): string
    {
        $payload = [
            'name' => $data['name'],
            'sku'  => $data['sku'],
            'rate' => (float) $data['price'],
        ];

        $response = $this->zoho->post('/items', $payload);

        if (!isset($response['item']['item_id'])) {
            throw new \Exception('Failed to create Zoho item');
        }

        return $response['item']['item_id'];
    }
}
