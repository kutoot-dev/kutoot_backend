<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * UserProfileImageSeeder - Adds profile images to existing users.
 *
 * DEV ONLY: Downloads HD avatar images for user profiles.
 */
class UserProfileImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Downloading and optimizing user profile images...');

        // Avatar picsum IDs for diverse realistic user avatars
        $avatarPicsumIds = [
            64, 65, 91, 175, 177, 219, 275, 334, 338, 342,
            433, 447, 453, 473, 506, 516, 532, 550, 556, 557,
            659, 660, 661, 669, 815, 823, 836, 839, 852, 856,
        ];

        $users = User::whereNull('image')->orWhere('image', '')->get();

        if ($users->isEmpty()) {
            // If all users have images, update some anyway
            $users = User::limit(20)->get();
        }

        $count = 0;
        foreach ($users as $index => $user) {
            $picsumId = $avatarPicsumIds[$index % count($avatarPicsumIds)];

            $imagePath = ImageSeederHelper::ensureImage(
                'users',
                'user-' . $user->id . '-avatar',
                'avatar',
                $picsumId
            );

            $user->update(['image' => $imagePath]);
            $count++;

            $this->command->line("  ✓ User: {$user->name}");
        }

        $this->command->info("UserProfileImageSeeder completed. {$count} user profile images seeded.");
    }
}
