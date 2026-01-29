<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * OrderSeeder - Seeds sample orders for testing.
 *
 * DEV ONLY: Creates sample orders with products and addresses for all status types.
 */
class OrderSeeder extends Seeder
{
    /**
     * Order status constants matching the system:
     * 0 = Pending, 1 = Progress, 2 = Delivered, 3 = Completed, 4 = Declined, 5 = Cancelled
     */
    private const ORDER_STATUSES = [
        0 => ['name' => 'Pending', 'payment_status' => 0],
        1 => ['name' => 'Progress', 'payment_status' => 1],
        2 => ['name' => 'Delivered', 'payment_status' => 1],
        3 => ['name' => 'Completed', 'payment_status' => 1],
        4 => ['name' => 'Declined', 'payment_status' => 0],
        5 => ['name' => 'Cancelled', 'payment_status' => 0],
    ];

    private const PAYMENT_TYPES = ['COD', 'Online', 'Card', 'Razorpay', 'Stripe'];

    public function run(): void
    {
        $this->command->info('Seeding orders with different status types...');

        $users = User::all();
        $products = Product::all();
        $vendor = Vendor::first();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please run UserFactory and ProductSeeder first.');
            return;
        }

        $statusCounts = [];
        $ordersCreated = 0;

        // Create orders for each status type to ensure coverage
        foreach (self::ORDER_STATUSES as $statusCode => $statusInfo) {
            // Create 4 orders per status type (24 total)
            for ($i = 0; $i < 4; $i++) {
                $order = $this->createOrder($users, $products, $vendor, $statusCode, $statusInfo);
                $ordersCreated++;
                $statusCounts[$statusInfo['name']] = ($statusCounts[$statusInfo['name']] ?? 0) + 1;
            }
        }

        // Log status distribution
        $this->command->info('Order status distribution:');
        foreach ($statusCounts as $status => $count) {
            $this->command->line("  ✓ {$status}: {$count} orders");
        }

        $this->command->info("OrderSeeder completed. {$ordersCreated} orders seeded with products and addresses.");
    }

    /**
     * Create a single order with specified status.
     */
    private function createOrder($users, $products, $vendor, int $statusCode, array $statusInfo): Order
    {
        $user = $users->random();
        $orderProducts = $products->random(rand(1, 4));
        $subTotal = $orderProducts->sum('price');
        $shippingCost = $subTotal > 100 ? 0 : 10;
        $tax = round($subTotal * 0.18, 2);
        $totalAmount = $subTotal + $shippingCost + $tax;

        $orderDate = $this->getOrderDateForStatus($statusCode);
        $paymentType = self::PAYMENT_TYPES[array_rand(self::PAYMENT_TYPES)];

        $orderData = [
            'user_id' => $user->id,
            'order_id' => 'ORD-' . strtoupper(uniqid()),
            'product_qty' => $orderProducts->count(),
            'total_amount' => $totalAmount,
            'sub_total' => $subTotal,
            'amount_real_currency' => $totalAmount,
            'amount_usd_currency' => $totalAmount,
            'coupon_discount' => rand(0, 1) ? rand(5, 50) : 0,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'order_status' => $statusCode,
            'payment_status' => $statusInfo['payment_status'],
            'payment_type' => $paymentType,
            'cash_on_delivery' => $paymentType === 'COD' ? 1 : 0,
            'order_date' => $orderDate,
            'order_month' => $orderDate->month,
            'order_year' => $orderDate->year,
        ];

        // Add status-specific dates
        $orderData = array_merge($orderData, $this->getStatusDates($statusCode, $orderDate));

        // Add cancellation data for cancelled/declined orders
        if ($statusCode === 4 || $statusCode === 5) {
            $orderData['cancel_reason'] = $this->getRandomCancelReason($statusCode);
            $orderData['cancelled_at'] = $orderDate->addDays(rand(1, 3));
        }

        $order = Order::create($orderData);

        // Create order address
        OrderAddress::create([
            'order_id' => $order->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '98' . rand(10000000, 99999999),
            'address' => 'House ' . rand(1, 999) . ', Street ' . rand(1, 50),
            'city' => $this->getRandomCity(),
            'state' => 'Delhi',
            'country' => 'India',
            'zip_code' => '1100' . rand(10, 99),
        ]);

        // Create order products
        foreach ($orderProducts as $product) {
            $qty = rand(1, 3);
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'vendor_id' => $vendor?->id ?? 1,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'qty' => $qty,
                'vat' => round($product->price * 0.18 * $qty, 2),
                'price' => $product->price * $qty,
            ]);
        }

        return $order;
    }

    /**
     * Get appropriate order date based on status.
     */
    private function getOrderDateForStatus(int $statusCode): \Carbon\Carbon
    {
        return match ($statusCode) {
            0 => now()->subDays(rand(0, 7)),      // Pending: recent orders
            1 => now()->subDays(rand(3, 14)),     // Progress: slightly older
            2 => now()->subDays(rand(7, 30)),     // Delivered: older orders
            3 => now()->subDays(rand(14, 60)),    // Completed: older orders
            4, 5 => now()->subDays(rand(7, 45)),  // Declined/Cancelled: any recent
            default => now()->subDays(rand(0, 90)),
        };
    }

    /**
     * Get status-specific dates.
     */
    private function getStatusDates(int $statusCode, \Carbon\Carbon $orderDate): array
    {
        $dates = [];

        if ($statusCode >= 1) {
            $dates['order_approval_date'] = $orderDate->copy()->addDays(rand(0, 1))->format('Y-m-d');
            $dates['payment_approval_date'] = $orderDate->copy()->addDays(rand(0, 1))->format('Y-m-d');
        }

        if ($statusCode === 2 || $statusCode === 3) {
            $dates['order_delivered_date'] = $orderDate->copy()->addDays(rand(3, 7))->format('Y-m-d');
        }

        if ($statusCode === 3) {
            $dates['order_completed_date'] = $orderDate->copy()->addDays(rand(5, 10))->format('Y-m-d');
        }

        if ($statusCode === 4) {
            $dates['order_declined_date'] = $orderDate->copy()->addDays(rand(1, 3))->format('Y-m-d');
        }

        return $dates;
    }

    /**
     * Get random cancellation reason.
     */
    private function getRandomCancelReason(int $statusCode): string
    {
        $cancelReasons = [
            'Customer requested cancellation',
            'Changed my mind',
            'Found better price elsewhere',
            'Ordered by mistake',
            'Delivery taking too long',
        ];

        $declineReasons = [
            'Payment verification failed',
            'Out of stock',
            'Suspected fraudulent order',
            'Address verification failed',
            'Unable to fulfill order',
        ];

        $reasons = $statusCode === 5 ? $cancelReasons : $declineReasons;
        return $reasons[array_rand($reasons)];
    }

    /**
     * Get random city.
     */
    private function getRandomCity(): string
    {
        $cities = ['New Delhi', 'Mumbai', 'Bangalore', 'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad'];
        return $cities[array_rand($cities)];
    }
}
