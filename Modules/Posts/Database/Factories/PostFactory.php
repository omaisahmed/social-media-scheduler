<?php

declare(strict_types=1);

namespace Modules\Posts\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Business\Models\Business;
use Modules\Posts\Models\Post;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'user_id' => null,
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(2, true),
            'hashtags' => '#Maqaam #SocialMedia',
            'status' => Post::STATUS_DRAFT,
            'scheduled_at' => null,
            'featured_media_id' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => Post::STATUS_SCHEDULED,
            'scheduled_at' => now()->addDays(1),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now()->subHour(),
        ]);
    }
}
