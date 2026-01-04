<?php
namespace App\Http\Controllers;
use App\Services\Zoho\ZohoSalesOrderService;
use App\Models\Order;

class ZohoController extends Controller
{
    public function syncOrder(Order $order, ZohoSalesOrderService $service)
    {
        try {
            $salesOrderId = $service->createSalesOrder($order);

            return response()->json([
                'message' => 'Zoho sync successful',
                'zoho_salesorder_id' => $salesOrderId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
