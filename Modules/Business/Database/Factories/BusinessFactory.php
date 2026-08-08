<?php

declare(strict_types=1);

namespace Modules\Business\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Business\Models\Business;

/**
 * @extends Factory<Business>
 */
final class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(2),
            'website_url' => fake()->url(),
            'industry' => fake()->randomElement(['SaaS', 'Retail', 'Agency', 'E-commerce']),
            'description' => fake()->sentence(),
            'primary_timezone' => 'UTC',
            'default_locale' => 'en',
            'theme_color' => '#6366f1',
        ];
    }
}
