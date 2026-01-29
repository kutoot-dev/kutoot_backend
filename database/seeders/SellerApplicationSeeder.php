<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Store\SellerApplication;
use App\Models\Admin;
use Illuminate\Database\Seeder;

/**
 * SellerApplicationSeeder - Creates demo seller/store applications for testing.
 *
 * Creates applications in various statuses:
 * - PENDING: Newly submitted applications
 * - VERIFIED: Applications verified by admin (awaiting approval)
 * - APPROVED: Approved applications (seller account created)
 * - REJECTED: Rejected applications
 */
class SellerApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding store applications...');

        // Get admin for verification/approval references
        $admin = Admin::first();
        $adminId = $admin?->id;

        $applications = [
            // PENDING applications
            [
                'store_name' => 'Spice Garden Restaurant',
                'owner_mobile' => '9876543210',
                'owner_email' => 'spicegarden@example.com',
                'store_type' => 'Restaurant',
                'store_address' => 'Plot 45, Jubilee Hills, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4326,
                'lng' => 78.4071,
                'min_bill_amount' => 300.00,
                'status' => SellerApplication::STATUS_PENDING,
            ],
            [
                'store_name' => 'Fresh Bakes Cafe',
                'owner_mobile' => '9876543211',
                'owner_email' => 'freshbakes@example.com',
                'store_type' => 'Cafe',
                'store_address' => 'Shop 12, Banjara Hills Road 10, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4156,
                'lng' => 78.4347,
                'min_bill_amount' => 150.00,
                'status' => SellerApplication::STATUS_PENDING,
            ],
            [
                'store_name' => 'Urban Grocery Mart',
                'owner_mobile' => '9876543212',
                'owner_email' => 'urbangrocery@example.com',
                'store_type' => 'Grocery',
                'store_address' => '23-A, Kukatpally Housing Board, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4948,
                'lng' => 78.3996,
                'min_bill_amount' => 200.00,
                'status' => SellerApplication::STATUS_PENDING,
            ],

            // VERIFIED applications (awaiting final approval)
            [
                'store_name' => 'Glam Studio Salon',
                'owner_mobile' => '9876543213',
                'owner_email' => 'glamstudio@example.com',
                'store_type' => 'Salon',
                'store_address' => 'First Floor, Madhapur Main Road, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4435,
                'lng' => 78.3772,
                'min_bill_amount' => 500.00,
                'status' => SellerApplication::STATUS_VERIFIED,
                'verified_by' => $adminId,
                'verification_notes' => 'All documents verified. Store location confirmed.',
                'verified_at' => now()->subDays(2),
            ],
            [
                'store_name' => 'Tech Zone Electronics',
                'owner_mobile' => '9876543214',
                'owner_email' => 'techzone@example.com',
                'store_type' => 'Electronics',
                'store_address' => 'Shop 5, CTC Complex, Secunderabad',
                'state' => 'Telangana',
                'city' => 'Secunderabad',
                'country' => 'India',
                'lat' => 17.4399,
                'lng' => 78.4983,
                'min_bill_amount' => 1000.00,
                'status' => SellerApplication::STATUS_VERIFIED,
                'verified_by' => $adminId,
                'verification_notes' => 'GST and trade license verified.',
                'verified_at' => now()->subDays(1),
            ],

            // APPROVED applications
            [
                'store_name' => 'Wellness Spa & Therapy',
                'owner_mobile' => '9876543215',
                'owner_email' => 'wellnessspa@example.com',
                'store_type' => 'Spa',
                'store_address' => 'Level 2, Forum Mall, Kukatpally, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4858,
                'lng' => 78.4004,
                'min_bill_amount' => 800.00,
                'commission_percent' => 10.00,
                'discount_percent' => 5.00,
                'rating' => 4.50,
                'status' => SellerApplication::STATUS_APPROVED,
                'verified_by' => $adminId,
                'verification_notes' => 'All documents verified.',
                'verified_at' => now()->subDays(7),
                'approved_by' => $adminId,
                'seller_email' => 'wellnessspa@example.com',
                'approved_at' => now()->subDays(5),
            ],
            [
                'store_name' => 'FitLife Gym',
                'owner_mobile' => '9876543216',
                'owner_email' => 'fitlifegym@example.com',
                'store_type' => 'Gym',
                'store_address' => 'Opposite KPHB Metro Station, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4702,
                'lng' => 78.3911,
                'min_bill_amount' => 1500.00,
                'commission_percent' => 8.00,
                'discount_percent' => 10.00,
                'rating' => 4.20,
                'status' => SellerApplication::STATUS_APPROVED,
                'verified_by' => $adminId,
                'verification_notes' => 'Location verified. All equipment in place.',
                'verified_at' => now()->subDays(10),
                'approved_by' => $adminId,
                'seller_email' => 'fitlifegym@example.com',
                'approved_at' => now()->subDays(8),
            ],

            // REJECTED applications
            [
                'store_name' => 'Quick Mart Express',
                'owner_mobile' => '9876543217',
                'owner_email' => 'quickmart@example.com',
                'store_type' => 'Grocery',
                'store_address' => 'Street 5, Ameerpet, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4375,
                'lng' => 78.4483,
                'min_bill_amount' => 100.00,
                'status' => SellerApplication::STATUS_REJECTED,
                'rejected_by' => $adminId,
                'rejection_reason' => 'Incomplete documentation. Business license expired.',
                'rejected_at' => now()->subDays(3),
            ],
            [
                'store_name' => 'Style Hub Fashion',
                'owner_mobile' => '9876543218',
                'owner_email' => 'stylehub@example.com',
                'store_type' => 'Fashion',
                'store_address' => 'Lane 2, Begumpet, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4411,
                'lng' => 78.4629,
                'min_bill_amount' => 500.00,
                'status' => SellerApplication::STATUS_REJECTED,
                'rejected_by' => $adminId,
                'rejection_reason' => 'Location does not meet minimum requirements for retail operation.',
                'rejected_at' => now()->subDays(5),
            ],

            // More PENDING for pagination testing
            [
                'store_name' => 'Pet Paradise',
                'owner_mobile' => '9876543219',
                'owner_email' => 'petparadise@example.com',
                'store_type' => 'Pet Store',
                'store_address' => 'Building 8, Gachibowli, Hyderabad',
                'state' => 'Telangana',
                'city' => 'Hyderabad',
                'country' => 'India',
                'lat' => 17.4401,
                'lng' => 78.3489,
                'min_bill_amount' => 250.00,
                'status' => SellerApplication::STATUS_PENDING,
            ],
        ];

        foreach ($applications as $applicationData) {
            // Generate unique application ID
            $applicationData['application_id'] = SellerApplication::generateApplicationId();

            // Try to download a store image
            try {
                $imagePath = ImageSeederHelper::ensureImage(
                    'store-applications',
                    'store-' . strtolower(str_replace(' ', '-', $applicationData['store_name'])),
                    'store',
                    rand(100, 500)
                );
                $applicationData['store_image'] = $imagePath;
            } catch (\Exception $e) {
                // Image download failed, continue without image
            }

            SellerApplication::query()->create($applicationData);

            $this->command->line("  ✓ {$applicationData['store_name']} ({$applicationData['status']})");
        }

        // Summary
        $counts = [
            'PENDING' => SellerApplication::where('status', SellerApplication::STATUS_PENDING)->count(),
            'VERIFIED' => SellerApplication::where('status', SellerApplication::STATUS_VERIFIED)->count(),
            'APPROVED' => SellerApplication::where('status', SellerApplication::STATUS_APPROVED)->count(),
            'REJECTED' => SellerApplication::where('status', SellerApplication::STATUS_REJECTED)->count(),
        ];

        $this->command->info('');
        $this->command->info('SellerApplicationSeeder completed. ' . count($applications) . ' applications seeded.');
        $this->command->info("  - PENDING: {$counts['PENDING']}");
        $this->command->info("  - VERIFIED: {$counts['VERIFIED']}");
        $this->command->info("  - APPROVED: {$counts['APPROVED']}");
        $this->command->info("  - REJECTED: {$counts['REJECTED']}");
    }
}
