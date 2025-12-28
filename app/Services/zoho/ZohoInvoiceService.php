<?php

namespace App\Services\Zoho;

use App\Models\Order;
use App\Services\Zoho\ZohoCustomerService;

class ZohoInvoiceService
{
    protected ZohoClient $zoho;
    protected ZohoCustomerService $customerService;

    public function __construct(
        ZohoClient $zoho,
        ZohoCustomerService $customerService
    ) {
        $this->zoho = $zoho;
        $this->customerService = $customerService;
    }

    public function createInvoice(Order $order): string
{
    if (!$order->zoho_salesorder_id) {
        throw new \Exception('Zoho Sales Order ID missing.');
    }

    $response = $this->zoho->post(
        "/salesorders/{$order->zoho_salesorder_id}/converttoinvoice",
        []
    );

    if (!isset($response['invoice']['invoice_id'])) {
        throw new \Exception(
            'Failed to convert Sales Order to Invoice: ' . json_encode($response)
        );
    }

    $order->update([
        'zoho_invoice_id' => $response['invoice']['invoice_id'],
    ]);

    return $response['invoice']['invoice_id'];
}

}
