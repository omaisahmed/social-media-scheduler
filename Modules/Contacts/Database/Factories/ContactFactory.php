<?php

declare(strict_types=1);

namespace Modules\Contacts\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Business\Models\Business;
use Modules\Contacts\Models\Contact;

/**
 * @extends Factory<Contact>
 */
final class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->name(),
            'avatar_url' => fake()->imageUrl(64, 64),
        ];
    }

    public function withHandles(array $handles = []): static
    {
        return $this->afterCreating(function (Contact $contact) use ($handles) {
            $handles === [] && $handles = [['platform' => 'facebook', 'handle' => '@'.fake()->userName()]];

            foreach ($handles as $handle) {
                $contact->handles()->create($handle);
            }
        });
    }
}
