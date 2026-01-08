<?php

namespace App\Services\Zoho;

use App\Models\Order;

class ZohoInvoiceService
{
    protected ZohoClient $zoho;

    public function __construct(ZohoClient $zoho)
    {
        $this->zoho = $zoho;
    }

    /**
     * Create Zoho Books Invoice
     */
    public function createInvoice(Order $order): string
    {
        // 1. Customer check
        if (!$order->user || !$order->user->zoho_customer_id) {
            throw new \Exception('Zoho customer_id missing for user');
        }

        // 2. Prepare line items from order_products
        $lineItems = [];

        foreach ($order->items as $orderItem) {

            if (
                !$orderItem->product ||
                !$orderItem->product->zoho_item_id
            ) {
                throw new \Exception(
                    'Zoho item_id missing for product ID ' . $orderItem->product_id
                );
            }

            $lineItems[] = [
                'item_id'  => $orderItem->product->zoho_item_id,
                'rate'     => (float) $orderItem->unit_price,
                'quantity' => (int) $orderItem->qty,
            ];
        }

        if (empty($lineItems)) {
            throw new \Exception('No valid line items for invoice');
        }

        // ✅ 3. Invoice payload
        $payload = [
            'customer_id'       => $order->user->zoho_customer_id,
            'line_items'        => $lineItems,
            'reference_number'  => 'ORDER-' . $order->id,
        ];
        // 4. Create invoice
        
        $response = $this->zoho->post('/invoices', $payload);
        
        if (!isset($response['invoice']['invoice_id'])) {
            throw new \Exception(
                'Zoho invoice creation failed: ' . json_encode($response)
            );
        }

        // 5. Save invoice ID
        $order->update([
            'zoho_invoice_id' => $response['invoice']['invoice_id'],
        ]);

        return $response['invoice']['invoice_id'];
    }
}