<?php

namespace App\Services\Zoho;

use App\Models\Order;

class ZohoSalesOrderService
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

    public function createSalesOrder(Order $order): string
    {
        
        if (!$order->user) {
            throw new \Exception('Order user not found.');
        }

        // STEP 1: Get or create Zoho customer
        $customerId = $this->customerService->getOrCreate($order->user);
        
        // STEP 2: Prepare line items (CORRECT MAPPING)
        $lineItems = [];

        foreach ($order->orderProducts as $orderItem) {
            // Fetch product to get zoho_item_id
            $product = \App\Models\Product::find($orderItem->product_id);

            if (!$product || !$product->zoho_item_id) {
                throw new \Exception(
                    "Zoho item not found for product_id {$orderItem->product_id}"
                );
            }

            $lineItems[] = [
                'item_id'  => $product->zoho_item_id,   // ✅ REQUIRED
                'quantity' => (int) $orderItem->qty,    // ✅ correct field
                'rate'     => (float) $orderItem->unit_price, // ✅ correct field
            ];
        
        }
        

        if (empty($lineItems)) {
            throw new \Exception('Order has no products.');
        }

        // STEP 3: Create Zoho Sales Order
        $payload = [
            'customer_id' => $customerId,
            'line_items'  => $lineItems,
            'status'      => 'confirmed',

            // ✅ REQUIRED FOR INDIA
            'is_tax_inclusive'    => false,
            'tax_exemption_code' => 'GST_EXEMPT',
        ];

        $response = $this->zoho->post('/salesorders', $payload);
        // print_r($response); die;
        if (!isset($response['salesorder']['salesorder_id'])) {
            throw new \Exception('Failed to create Zoho Sales Order.');
        }

        // STEP 4: Save Zoho Sales Order ID
        $order->update([
            'zoho_salesorder_id' => $response['salesorder']['salesorder_id'],
            'order_status'       => 'CONFIRMED',
        ]);

        return $response['salesorder']['salesorder_id'];
    
    }

}
