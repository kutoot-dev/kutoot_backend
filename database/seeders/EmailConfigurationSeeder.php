<?php

namespace Database\Seeders;

use App\Models\EmailConfiguration;
use Illuminate\Database\Seeder;

/**
 * EmailConfigurationSeeder - Seeds email/SMTP configuration from .env
 *
 * DEV ONLY: Uses SendGrid SMTP settings from .env file.
 */
class EmailConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding email configuration...');

        EmailConfiguration::updateOrCreate(
            ['id' => 1],
            [
                'mail_type' => '1',
                'mail_host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'mail_port' => env('MAIL_PORT', '587'),
                'email' => env('MAIL_FROM_ADDRESS', 'noreply@kutoot.com'),
                'email_password' => null,
                'smtp_username' => env('MAIL_USERNAME', 'noreply@kutoot.com'),
                'smtp_password' => env('MAIL_PASSWORD','ftgvvegmppakzltm'),
                'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            ]
        );

        $this->command->info('  ✓ Email configuration seeded (SendGrid SMTP)');
    }
}
