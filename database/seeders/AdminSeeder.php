<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder - Creates default admin accounts for development/testing.
 *
 * DEV ONLY - Do not use these credentials in production.
 *
 * Default Login Credentials:
 * - Email: admin@kutoot.com
 * - Password: password
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'it@kutoot.com',
                'password' => Hash::make('kutoot@123'),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin2@kutoot.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::query()->firstOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        $this->command->info('AdminSeeder completed. 2 admin accounts created.');
        $this->command->info('  - admin@kutoot.com / password (Super Admin)');
        $this->command->info('  - admin2@kutoot.com / password (Admin)');
    }
}
