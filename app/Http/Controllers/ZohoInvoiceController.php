<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\Zoho\ZohoInvoiceService;
use Illuminate\Support\Facades\DB;

class ZohoInvoiceController extends Controller
{
    public function handlePaymentSuccess(
        Request $request,
        ZohoInvoiceService $zohoInvoiceService
    ) {
        $request->validate([
            'order_id'       => 'required|integer',
            'payment_status' => 'required|in:success',
        ]);

        $order = Order::with(['user', 'items.product'])->findOrFail($request->order_id);
        
        // Prevent duplicate invoice
        if ($order->zoho_invoice_id) {
            return response()->json([
                'message'    => 'Invoice already created',
                'invoice_id' => $order->zoho_invoice_id,
            ]);
        }

        DB::beginTransaction();

        try {
            // Mark payment paid
            $order->update([
                'payment_status' => 'paid',
            ]);

            // Create invoice
            $invoiceId = $zohoInvoiceService->createInvoice($order);

            DB::commit();

            return response()->json([
                'message'    => 'Invoice created successfully',
                'invoice_id' => $invoiceId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Invoice creation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
