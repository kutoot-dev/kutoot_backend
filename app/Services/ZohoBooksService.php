<?php

namespace App\Services\Zoho;

use App\Models\Order;
use Exception;

class ZohoBooksService
{
    protected ZohoClient $zoho;

    public function __construct(ZohoClient $zoho)
    {
        $this->zoho = $zoho;
    }

    public function createInvoice(Order $order, string $customerId): string
    {
        if (!$customerId) {
            throw new Exception('Zoho customer_id missing');
        }

        $payload = [
            'customer_id' => $customerId,
            'line_items' => [
                [
                    'name'     => 'Order #' . $order->id,
                    'rate'     => (float) $order->total_amount,
                    'quantity' => 1,
                ]
            ],
        ];

        $response = $this->zoho->post('/invoices', $payload);

        if (!isset($response['invoice']['invoice_id'])) {
            throw new Exception(
                'Zoho invoice creation failed: ' . json_encode($response)
            );
        }

        return $response['invoice']['invoice_id'];
    }
}
