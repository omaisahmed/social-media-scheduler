<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MediaLibrary\Models\MediaAsset;

/**
 * @extends Factory<MediaAsset>
 */
final class MediaAssetFactory extends Factory
{
    protected $model = MediaAsset::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \Modules\Business\Models\Business::factory(),
            'user_id' => null,
            'original_name' => fake()->word().'.png',
            'path' => 'media/test.png',
            'disk' => 'public',
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(1000, 500000),
            'width' => fake()->numberBetween(200, 2000),
            'height' => fake()->numberBetween(200, 2000),
            'type' => \Modules\MediaLibrary\Models\MediaAsset::TYPE_IMAGE,
        ];
    }
}
