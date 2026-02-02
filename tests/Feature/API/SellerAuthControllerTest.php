<?php

namespace Tests\Feature\API;

use App\Models\Store\Seller;
use App\Models\Store\SellerBankAccount;
use App\Models\Store\SellerApplication;
use App\Models\Store\StoreCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a store category for testing
        StoreCategory::create(['name' => 'Test Category', 'is_active' => true]);
    }

    /** @test */
    public function login_endpoint_returns_bank_details()
    {
        // Create a seller with approved application and bank details
        $seller = Seller::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
        ]);

        SellerApplication::factory()->create([
            'seller_id' => $seller->id,
            'status' => SellerApplication::STATUS_APPROVED,
            'store_name' => 'Test Store',
            'shop_code' => 'TEST123',
        ]);

        SellerBankAccount::factory()->create([
            'seller_id' => $seller->id,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'ifsc' => 'TEST0000001',
            'upi_id' => 'test@upi',
            'beneficiary_name' => 'Test Beneficiary',
        ]);

        // Attempt to login
        $response = $this->postJson('/api/seller/auth/login', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        // Verify the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'categories',
                    'seller' => [
                        'sellerId',
                        'shopId',
                        'shopName',
                        'ownerName',
                        'email',
                        'phone',
                        'status',
                    ],
                    'bankDetails' => [
                        'bankName',
                        'accountNumber',
                        'ifsc',
                        'upiId',
                        'beneficiaryName',
                    ],
                ],
            ]);

        // Verify bank details are returned and account number is masked
        $response->assertJson([
            'success' => true,
            'data' => [
                'bankDetails' => [
                    'bankName' => 'Test Bank',
                    'accountNumber' => 'XXXXXX7890',
                    'ifsc' => 'TEST0000001',
                    'upiId' => 'test@upi',
                    'beneficiaryName' => 'Test Beneficiary',
                ],
            ],
        ]);
    }

    /** @test */
    public function me_endpoint_returns_bank_details()
    {
        // Create a seller with approved application and bank details
        $seller = Seller::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
        ]);

        SellerApplication::factory()->create([
            'seller_id' => $seller->id,
            'status' => SellerApplication::STATUS_APPROVED,
            'store_name' => 'Test Store',
            'shop_code' => 'TEST123',
        ]);

        SellerBankAccount::factory()->create([
            'seller_id' => $seller->id,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'ifsc' => 'TEST0000001',
            'upi_id' => 'test@upi',
            'beneficiary_name' => 'Test Beneficiary',
        ]);

        // Attempt to login first to get token
        $loginResponse = $this->postJson('/api/seller/auth/login', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        // Use token to call me() endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/seller/me');

        // Verify the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'sellerId',
                    'shopId',
                    'shopName',
                    'ownerName',
                    'email',
                    'phone',
                    'bankDetails' => [
                        'bankName',
                        'accountNumber',
                        'ifsc',
                        'upiId',
                        'beneficiaryName',
                    ],
                ],
            ]);

        // Verify bank details are returned and account number is masked
        $response->assertJson([
            'success' => true,
            'data' => [
                'bankDetails' => [
                    'bankName' => 'Test Bank',
                    'accountNumber' => 'XXXXXX7890',
                    'ifsc' => 'TEST0000001',
                    'upiId' => 'test@upi',
                    'beneficiaryName' => 'Test Beneficiary',
                ],
            ],
        ]);
    }
}
