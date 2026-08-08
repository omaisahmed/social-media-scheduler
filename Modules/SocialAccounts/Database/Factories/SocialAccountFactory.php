<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SocialAccounts\Models\SocialAccount;

/**
 * @extends Factory<SocialAccount>
 */
final class SocialAccountFactory extends Factory
{
    protected $model = SocialAccount::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \Modules\Business\Models\Business::factory(),
            'platform' => fake()->randomElement(SocialAccount::PLATFORMS),
            'account_name' => fake()->name(),
            'account_identifier' => (string) fake()->unique()->randomNumber(8),
            'avatar_url' => fake()->imageUrl(64, 64),
            'is_connected' => true,
            'connected_at' => now(),
        ];
    }

    public function platform(string $platform): static
    {
        return $this->state(fn () => ['platform' => $platform]);
    }
}
