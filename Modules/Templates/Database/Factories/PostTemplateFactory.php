<?php

declare(strict_types=1);

namespace Modules\Templates\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Templates\Models\PostTemplate;

/**
 * @extends Factory<PostTemplate>
 */
final class PostTemplateFactory extends Factory
{
    protected $model = PostTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \Modules\Business\Models\Business::factory(),
            'user_id' => null,
            'name' => fake()->words(3, true),
            'content' => fake()->paragraphs(2, true),
            'tags' => [fake()->word()],
        ];
    }
}
