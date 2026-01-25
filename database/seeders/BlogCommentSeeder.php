<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogComment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * BlogCommentSeeder - Seeds blog comments for blog posts.
 *
 * DEV ONLY: Creates sample comments for existing blog posts.
 */
class BlogCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding blog comments...');

        $blogs = Blog::all();

        if ($blogs->isEmpty()) {
            $this->command->warn('No blog posts found. Please run BlogSeeder first.');
            return;
        }

        // Sample commenters
        $commenters = [
            ['name' => 'John Smith', 'email' => 'john.smith@example.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.j@example.com'],
            ['name' => 'Mike Wilson', 'email' => 'mike.w@example.com'],
            ['name' => 'Emily Davis', 'email' => 'emily.d@example.com'],
            ['name' => 'David Brown', 'email' => 'david.b@example.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com'],
            ['name' => 'James Taylor', 'email' => 'james.t@example.com'],
            ['name' => 'Jennifer Martinez', 'email' => 'jennifer.m@example.com'],
            ['name' => 'Robert Garcia', 'email' => 'robert.g@example.com'],
            ['name' => 'Amanda Thompson', 'email' => 'amanda.t@example.com'],
            ['name' => 'Chris Lee', 'email' => 'chris.lee@example.com'],
            ['name' => 'Priya Sharma', 'email' => 'priya.s@example.com'],
            ['name' => 'Alex Chen', 'email' => 'alex.chen@example.com'],
            ['name' => 'Maria Rodriguez', 'email' => 'maria.r@example.com'],
            ['name' => 'Kevin Patel', 'email' => 'kevin.p@example.com'],
        ];

        // Sample comments for different blog types
        $commentTemplates = [
            'positive' => [
                'Great article! Very informative and well-written. Looking forward to more content like this.',
                'This is exactly what I was looking for. Thanks for sharing such valuable information!',
                'Excellent tips! I have been following this blog for a while and the quality is always top-notch.',
                'Really helpful guide. I shared this with my friends, and they loved it too!',
                'Amazing content as always! Keep up the great work.',
                'This helped me make a better decision. Thank you so much for the detailed explanation.',
                'I appreciate the effort put into this article. Very comprehensive and easy to understand.',
                'Been a loyal reader for months now. This blog never disappoints!',
            ],
            'question' => [
                'Great article! Do you have any recommendations for beginners?',
                'Very interesting read. Could you elaborate more on the third point?',
                'Thanks for this! Is there a follow-up article planned on this topic?',
                'Love the content! Where can I find more information about this?',
                'Helpful tips! Do you have any budget-friendly alternatives to suggest?',
            ],
            'feedback' => [
                'Good read overall, but I think you could have covered the price comparison aspect more.',
                'Nice article! It would be great to see some video content on this topic as well.',
                'Informative post. Maybe consider adding some infographics for visual learners.',
                'Solid advice here. Would love to see a case study in the next article.',
            ],
            'experience' => [
                'I tried the tips mentioned here and they really work! Highly recommend following this advice.',
                'Based on my experience, this is spot on. These methods have helped me a lot.',
                'I have been using some of these strategies for a while and can confirm they are effective.',
                'This matches my own research. Great to see it all compiled in one place.',
            ],
        ];

        $commentsCreated = 0;

        foreach ($blogs as $blog) {
            // Each blog gets 3-8 comments
            $numComments = rand(3, 8);

            for ($i = 0; $i < $numComments; $i++) {
                $commenter = $commenters[array_rand($commenters)];

                // Pick a random comment type and template
                $types = array_keys($commentTemplates);
                $type = $types[array_rand($types)];
                $comments = $commentTemplates[$type];
                $comment = $comments[array_rand($comments)];

                // Random date in the past 90 days
                $createdAt = Carbon::now()->subDays(rand(1, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                BlogComment::firstOrCreate(
                    [
                        'blog_id' => $blog->id,
                        'email' => $commenter['email'],
                        'comment' => $comment,
                    ],
                    [
                        'blog_id' => $blog->id,
                        'name' => $commenter['name'],
                        'email' => $commenter['email'],
                        'comment' => $comment,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                $commentsCreated++;
            }

            $this->command->line("  ✓ {$numComments} comments for: {$blog->title}");
        }

        $this->command->info("BlogCommentSeeder completed. {$commentsCreated} comments seeded.");
    }
}
