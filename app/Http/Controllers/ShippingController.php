<?php 
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function shipOrder($orderId, ShiprocketService $shiprocket)
    {
        // $order = Order::findOrFail($orderId);
        $order = Order::with('address')->findOrFail($orderId);

        $address = $order->address;

        if (!$address) {
            return response()->json([
                'error' => 'Order address not found'
            ], 422);
        }
        $cod = in_array(
        strtolower($order->payment_method),
        ['cod', 'cash on delivery']
    );

        $courier = $shiprocket->checkCourier([
            'pickup_location'   => 'warehouse',
            'pickup_postcode'   => '333333333',   // MUST be different city
            // 'delivery_postcode' => $address->shipping_zip_code ?: '560001',
            'delivery_postcode' => '334444444',
            // 'delivery_postcode' => '572101',
            'weight'            => 0.5,
            'cod'               => $cod,
            'declared_value'    => max($order->amount, 100),
        ]);

        $couriers = $courier['data']['available_courier_companies'] ?? [];
        if (count($couriers) === 0) {
            return response()->json([
                'error' => 'No courier available',
                'reason' => 'Pickup and delivery pincode not serviceable or same city',
                'payload' => $courier
            ], 422);
        }

        $phone = preg_replace('/\D/', '', $address->shipping_phone);
if (strlen($phone) !== 10) {
    $phone = '9999999999';
}

// $shipment = $shiprocket->createOrder([
//     'order_id' => 'ORD-' . $order->id . '-' . time(),
//     'order_date' => now()->format('Y-m-d'),

//     'pickup_location' => 'warehouse',

//     // ======================
//     // BILLING (MANDATORY)
//     // ======================
//     'billing_customer_name' => trim(
//         ($address->billing_first_name ?? '') . ' ' . ($address->billing_last_name ?? '')
//     ) ?: 'Customer Name',

//     'billing_address' => $address->billing_address ?: 'Customer Address Line 1',
//     'billing_city' => $address->city ?: 'Bangalore',
//     'billing_pincode' => $address->billing_zip_code ?: '560001',
//     'billing_state' => $address->billing_state ?: 'Karnataka',
//     'billing_country' => 'India',

//     'billing_email' => $address->billing_email ?: 'test@example.com',
//     'billing_phone' => $phone,

//     // ======================
//     // SHIPPING (🔥 REQUIRED EVEN IF SAME)
//     // ======================
//     'shipping_customer_name' => trim(
//         ($address->billing_first_name ?? '') . ' ' . ($address->billing_last_name ?? '')
//     ) ?: 'Customer Name',

//     'shipping_address' => $address->billing_address ?: 'Customer Address Line 1',
//     'shipping_city' => $address->city ?: 'Bangalore',
//     'shipping_pincode' => $address->billing_zip_code ?: '560001',
//     'shipping_state' => $address->billing_state ?: 'Karnataka',
//     'shipping_country' => 'India',

//     'shipping_email' => $address->billing_email ?: 'test@example.com',
//     'shipping_phone' => $phone,

//     'shipping_is_billing' => true,

//     // ======================
//     // ITEMS
//     // ======================
//     'order_items' => [
//         [
//             'name' => 'Product',
//             'sku' => 'SKU-' . $order->id,
//             'units' => 1,
//             'selling_price' => (int) $order->total_amount,
//         ]
//     ],

//     // ======================
//     // PAYMENT
//     // ======================
//     'payment_method' =>
//         strtolower($order->payment_method) === 'cash on delivery'
//             ? 'COD'
//             : 'Prepaid',

//     'sub_total' => (int) $order->total_amount,
//     'weight' => 0.5,
// ]);
        $fullName = trim(
            ($address->shipping_first_name ?? '') . ' ' . ($address->shipping_last_name ?? '')
        );

        if ($fullName === '') {
            $fullName = 'Customer User';
        }

        $shipment = $shiprocket->createOrder([
            'order_id' => 'ORD-' . $order->id . '-' . time(),
            'order_date' => now()->format('Y-m-d'),

            'pickup_location' => 'warehouse',

            // ======================
            // BILLING (ALL REQUIRED)
            // ======================
            'billing_customer_name' => $fullName,
            'billing_first_name'    => $address->shipping_first_name ?: 'Customer',
            'billing_last_name'     => $address->shipping_last_name ?: 'User',

            'billing_address' => $address->shipping_address ?: 'Address line',
            'billing_city'    => $address->shipping_city ?: 'Bangalore',
            'billing_pincode' => $address->shipping_zip_code ?: '560001',
            'billing_state'   => $address->shipping_state ?: 'Karnataka',
            'billing_country' => 'India',

            'billing_email' => $address->shipping_email ?: 'test@example.com',
            'billing_phone' => $phone,

            // ======================
            // SHIPPING
            // ======================
            'shipping_is_billing' => true,
            'shipping_phone' => $phone,

            // ======================
            // ITEMS
            // ======================
            'order_items' => [
                [
                    'name' => 'Product',
                    'sku'  => 'SKU-' . $order->id,
                    'units' => 1,
                    'selling_price' => (int) $order->total_amount,
                ]
            ],

            // ======================
            // PAYMENT
            // ======================
            'payment_method' =>
                strtolower($order->payment_method) === 'cash on delivery'
                    ? 'COD'
                    : 'Prepaid',

            'sub_total' => (int) $order->total_amount,

            // ======================
            // 📦 PACKAGE (MANDATORY)
            // ======================
            'length'  => 10,
            'breadth' => 10,
            'height'  => 10,
            'weight'  => 0.5,
        ]);

        // 3️⃣ Save shipment_id to order
        $shipmentId = $shipment['shipment_id'];
        $order->shipping_status = 'created';
        // $order->save();

        // Generate AWB (NEXT STEP)
        $awb = $shiprocket->generateAwb($shipmentId);
        $order->awb_code = $awb['awb_code'] ?? null;
        $order->courier_name = $awb['courier_name'] ?? null;
        $order->shipping_status = 'shipped';
        // $order->save();

        return response()->json([
            'message' => 'Shipment created successfully',
            'shipment_id' => $shipmentId
        ]);
    }

    // checkpincodeserviceability
    public function checkPincode(Request $request, ShiprocketService $shiprocket)
    {
        $request->validate([
            'pincode' => 'required|digits:6',
        ]);

        // Seller / warehouse pincode
        $pickupPincode = 400001;

        $isServiceable = $shiprocket->checkPincodeServiceability(
            $pickupPincode,
            (int) $request->pincode
        );

        return response()->json([
            'pincode'     => $request->pincode,
            'serviceable' => $isServiceable,
        ]);
    }

}
