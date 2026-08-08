<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Business\Models\Business;
use Modules\Posts\Models\Post;
use Modules\Posts\Models\PostAccount;
use Modules\SocialAccounts\Models\SocialAccount;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Demo User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );

        $business = Business::withoutBusinessScope(fn () => Business::firstOrCreate(
            ['name' => 'Demo Business'],
            ['slug' => 'demo-business', 'timezone' => 'UTC'],
        ));

        $user->update(['business_id' => $business->getKey()]);
        auth()->login($user);

        $accounts = collect(SocialAccount::PLATFORMS)->map(function ($platform) use ($business) {
            $names = [
                'facebook' => 'Pixel & Pine',
                'instagram' => '@pixelandpine',
                'linkedin' => 'Pixel & Pine Co.',
                'twitter' => '@pixelandpine',
                'pinterest' => 'pixelandpine',
            ];

            return SocialAccount::withoutBusinessScope(fn () => SocialAccount::firstOrCreate(
                ['business_id' => $business->getKey(), 'platform' => $platform],
                [
                    'account_name' => $names[$platform],
                    'account_identifier' => (string) fake()->unique()->randomNumber(8),
                    'avatar_url' => null,
                    'is_connected' => true,
                    'connected_at' => now()->subMonths(2),
                ],
            ));
        })->keyBy(fn ($account) => $account->platform);

        $published = [
            ['title' => '10 content ideas your brand can steal this month', 'daysAgo' => 6, 'accounts' => ['facebook', 'instagram']],
            ['title' => 'Behind the scenes: our studio setup', 'daysAgo' => 5, 'accounts' => ['instagram']],
            ['title' => 'New blog post — The 2026 social playbook', 'daysAgo' => 4, 'accounts' => ['linkedin', 'twitter']],
            ['title' => 'Product launch week starts today', 'daysAgo' => 3, 'accounts' => ['facebook', 'instagram', 'linkedin']],
            ['title' => '3 engagement tips that actually move the needle', 'daysAgo' => 2, 'accounts' => ['twitter', 'linkedin']],
            ['title' => 'Customer spotlight: how Loop.fit grew 3x', 'daysAgo' => 1, 'accounts' => ['facebook', 'linkedin']],
        ];

        foreach ($published as $index => $item) {
            $at = now()->subDays($item['daysAgo'])->setTime(9, 0, 0);
            $post = Post::withoutBusinessScope(fn () => Post::create([
                'business_id' => $business->getKey(),
                'user_id' => $user->getKey(),
                'title' => $item['title'],
                'content' => $this->content($item['title']),
                'status' => Post::STATUS_PUBLISHED,
                'scheduled_at' => $at,
                'published_at' => $at->addMinutes(5),
            ]));

            foreach ($item['accounts'] as $platform) {
                PostAccount::create([
                    'post_id' => $post->getKey(),
                    'social_account_id' => $accounts[$platform]->getKey(),
                    'platform' => $platform,
                    'status' => PostAccount::STATUS_PUBLISHED,
                    'published_at' => $at->addMinutes(5),
                ]);
            }
        }

        $upcoming = [
            ['title' => 'Weekly digest: everything you missed', 'when' => now()->addHours(4), 'status' => Post::STATUS_SCHEDULED, 'accounts' => ['facebook', 'instagram']],
            ['title' => 'New feature announcement', 'when' => now()->addDay()->setTime(11, 30), 'status' => Post::STATUS_SCHEDULED, 'accounts' => ['linkedin', 'twitter']],
            ['title' => 'Friday vibes: link in bio', 'when' => now()->addDays(2)->setTime(16, 0), 'status' => Post::STATUS_QUEUED, 'accounts' => ['instagram']],
            ['title' => 'Case study: automation at scale', 'when' => now()->addDays(3)->setTime(9, 15), 'status' => Post::STATUS_SCHEDULED, 'accounts' => ['linkedin']],
            ['title' => 'Community Q&A — drop your questions', 'when' => now()->addDays(4)->setTime(13, 45), 'status' => Post::STATUS_SCHEDULED, 'accounts' => ['twitter', 'facebook']],
        ];

        foreach ($upcoming as $index => $item) {
            $post = Post::withoutBusinessScope(fn () => Post::create([
                'business_id' => $business->getKey(),
                'user_id' => $user->getKey(),
                'title' => $item['title'],
                'content' => $this->content($item['title']),
                'status' => $item['status'],
                'scheduled_at' => $item['when'],
            ]));

            foreach ($item['accounts'] as $platform) {
                PostAccount::create([
                    'post_id' => $post->getKey(),
                    'social_account_id' => $accounts[$platform]->getKey(),
                    'platform' => $platform,
                    'status' => PostAccount::STATUS_PENDING,
                ]);
            }
        }

        $user->notify(new \Modules\Notifications\Notifications\ScheduledPostNotification([
            'title' => 'Post published',
            'message' => '"Customer spotlight: how Loop.fit grew 3x" went live on 2 platforms.',
            'url' => null,
        ]));

        $this->command?->info('Demo data seeded for '.$user->email);
    }

    private function content(string $title): string
    {
        return $title."\n\n".'This is a demo post generated by the DemoSeeder to showcase the social media scheduler.';
    }
}
