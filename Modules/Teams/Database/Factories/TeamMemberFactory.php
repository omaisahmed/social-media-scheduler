<?php

declare(strict_types=1);

namespace Modules\Teams\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Teams\Models\TeamMember;

/**
 * @extends Factory<TeamMember>
 */
final class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \Modules\Business\Models\Business::factory(),
            'user_id' => \App\Models\User::factory(),
            'role' => TeamMember::ROLE_MEMBER,
        ];
    }

    public function owner(): static
    {
        return $this->state(fn () => ['role' => TeamMember::ROLE_OWNER]);
    }
}
