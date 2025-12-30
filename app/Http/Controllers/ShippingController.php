<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Create shipment in Shiprocket
     */
    public function shipOrder(int $orderId, ShiprocketService $shiprocket)
    {
        $order = Order::with('address')->findOrFail($orderId);
        $address = $order->address;

        if (!$address) {
            return response()->json([
                'error' => 'Order address not found'
            ], 422);
        }

        // COD flag (Shiprocket expects 0/1)
        $isCod = in_array(
            strtolower($order->payment_method),
            ['cod', 'cash on delivery']
        ) ? 1 : 0;

        // ✅ USE REAL VALID PINCODES
        $pickupPincode   = 282006; // warehouse pincode
        $deliveryPincode = (int) ($address->shipping_zip_code ?? 560001);

        /**
         * 1️⃣ Check courier availability
         */
        $courierResponse = $shiprocket->checkCourier([
            'pickup_postcode'   => $pickupPincode,
            'delivery_postcode' => $deliveryPincode,
            'weight'            => 0.5,
            'cod'               => $isCod,
        ]);

        $couriers = $courierResponse['data']['available_courier_companies'] ?? [];

        if (empty($couriers)) {
            return response()->json([
                'error'  => 'No courier available',
                'reason' => 'Pickup or delivery pincode not serviceable',
                'data'   => $courierResponse,
            ], 422);
        }

        /**
         * 2️⃣ Sanitize phone
         */
        $phone = preg_replace('/\D/', '', $address->shipping_phone);
        if (strlen($phone) !== 10) {
            $phone = '9999999999';
        }

        /**
         * 3️⃣ Prepare customer name
         */
        $fullName = trim(
            ($address->shipping_first_name ?? '') . ' ' .
            ($address->shipping_last_name ?? '')
        );

        if ($fullName === '') {
            $fullName = 'Customer User';
        }

        /**
         * 4️⃣ Create shipment
         */
            $shipment = $shiprocket->createOrder([
            'order_id'   => 'ORD-' . $order->id . '-' . time(),
            'order_date' => now()->format('Y-m-d'),

            // ⚠️ MUST MATCH SHIPROCKET DASHBOARD
            'pickup_location' => 'warehouse',

            // ===== BILLING (ALL REQUIRED) =====
            'billing_customer_name' => $fullName,
            'billing_first_name'    => $address->shipping_first_name ?: 'Customer',
            'billing_last_name'     => $address->shipping_last_name ?: 'User',

            'billing_address' => $address->shipping_address ?: 'Address',
            'billing_city'    => $address->shipping_city ?: 'Bangalore',
            'billing_pincode' => $deliveryPincode,
            'billing_state'   => $address->shipping_state ?: 'Karnataka',
            'billing_country' => 'India',
            'billing_email'   => $address->shipping_email ?: 'test@example.com',
            'billing_phone'   => $phone,

            // ===== SHIPPING (MANDATORY EVEN IF SAME) =====
            'shipping_customer_name' => $fullName,
            'shipping_first_name'    => $address->shipping_first_name ?: 'Customer',
            'shipping_last_name'     => $address->shipping_last_name ?: 'User',

            'shipping_address' => $address->shipping_address ?: 'Address',
            'shipping_city'    => $address->shipping_city ?: 'Bangalore',
            'shipping_pincode' => $deliveryPincode,
            'shipping_state'   => $address->shipping_state ?: 'Karnataka',
            'shipping_country' => 'India',
            'shipping_email'   => $address->shipping_email ?: 'test@example.com',
            'shipping_phone'   => $phone,

            'shipping_is_billing' => true,

            // ===== ITEMS =====
            'order_items' => [
                [
                    'name'           => 'Product',
                    'sku'            => 'SKU-' . $order->id,
                    'units'          => 1,
                    'selling_price' => (int) $order->total_amount,
                ]
            ],

            // ===== PAYMENT =====
            'payment_method' => $isCod ? 'COD' : 'Prepaid',
            'sub_total'      => (int) $order->total_amount,

            // ===== PACKAGE (MANDATORY) =====
            'length'  => 10,
            'breadth' => 10,
            'height'  => 10,
            'weight'  => 0.5,
        ]);

        /**
         * 5️⃣ Save shipment info
         */
        $order->shipment_id     = $shipment['shipment_id'];
        $order->shipping_status = 'created';
        // $order->save();

        /**
         * 6️⃣ Generate AWB
         */
        $awbCode = $shiprocket->generateAwb($order->shipment_id);

        if ($awbCode) {
            $order->awb_code        = $awbCode;
            $order->shipping_status = 'shipped';
            // $order->save();
        }

        return response()->json([
            'message'     => 'Shipment created successfully',
            'shipment_id'=> $shipment['shipment_id'],
            'awb_code'   => $awbCode,
        ]);
    }

    /**
     * Check pincode serviceability
     */
    public function checkPincode(Request $request, ShiprocketService $shiprocket)
    {
        $request->validate([
            'pincode' => 'required|digits:6',
        ]);

        $pickupPincode = 282006; // warehouse pincode

        $serviceable = $shiprocket->checkPincodeServiceability(
            $pickupPincode,
            (int) $request->pincode
        );

        return response()->json([
            'pincode'     => $request->pincode,
            'serviceable' => $serviceable,
        ]);
    }
}
