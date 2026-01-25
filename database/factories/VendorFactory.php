<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        $shopName = $this->faker->company() . ' ' . $this->faker->randomElement(['Store', 'Shop', 'Mart', 'Outlet', 'Bazaar']);

        return [
            'user_id' => User::factory(),
            'shop_name' => $shopName,
            'slug' => Str::slug($shopName),
            'owner_name' => $this->faker->name(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'description' => $this->faker->paragraph(3),
            'greeting_msg' => $this->faker->sentence(),
            'open_at' => '09:00',
            'closed_at' => '21:00',
            'address_latitude' => $this->faker->latitude(20, 30),
            'address_longitude' => $this->faker->longitude(70, 90),
            'status' => 1,
            'is_featured' => $this->faker->boolean(30),
            'is_top' => $this->faker->boolean(20),
            'banner_image' => 'https://picsum.photos/seed/' . Str::slug($shopName) . '-banner/1200/400',
            'logo' => 'https://picsum.photos/seed/' . Str::slug($shopName) . '-logo/200/200',
        ];
    }
}
