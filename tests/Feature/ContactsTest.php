<?php

declare(strict_types=1);

use App\Models\User;
use Modules\Business\Models\Business;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\ContactHandle;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->user = User::factory()->create([
        'business_id' => $this->business->getKey(),
        'email_verified_at' => now(),
    ]);
});

test('a contact can be created with platform handles', function () {
    $this->actingAs($this->user)
        ->post(route('contacts.store'), [
            'name' => 'Jane Doe',
            'handles' => [
                'facebook' => ['handle' => '@janedoe', 'platform_uid' => '987654321', 'profile_url' => 'https://facebook.com/janedoe'],
                'instagram' => ['handle' => 'janedoe', 'platform_uid' => '', 'profile_url' => ''],
                'twitter' => ['handle' => '', 'platform_uid' => '', 'profile_url' => ''],
                'linkedin' => ['handle' => '', 'platform_uid' => '', 'profile_url' => ''],
            ],
        ])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('status', 'contact-created');

    $contact = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->first());

    expect($contact)->not->toBeNull()
        ->and($contact->name)->toBe('Jane Doe')
        ->and($contact->handles->count())->toBe(2)
        ->and($contact->handleFor('facebook')?->platform_uid)->toBe('987654321');
});

test('contacts are scoped to their business', function () {
    $otherBusiness = Business::factory()->create();

    Contact::factory()->create([
        'business_id' => $otherBusiness->getKey(),
        'name' => 'Other tenant person',
    ]);

    $this->actingAs($this->user)
        ->get(route('contacts.index'))
        ->assertOk()
        ->assertDontSee('Other tenant person');
});

test('a contact can be updated with new handles', function () {
    $contact = Contact::factory()->create([
        'business_id' => $this->business->getKey(),
        'name' => 'Old Name',
    ]);
    $contact->handles()->create(['platform' => 'facebook', 'handle' => '@old']);

    $this->actingAs($this->user)
        ->patch(route('contacts.update', $contact), [
            'name' => 'New Name',
            'handles' => [
                'facebook' => ['handle' => '@new', 'platform_uid' => '111', 'profile_url' => ''],
                'instagram' => ['handle' => '', 'platform_uid' => '', 'profile_url' => ''],
                'twitter' => ['handle' => '', 'platform_uid' => '', 'profile_url' => ''],
                'linkedin' => ['handle' => '', 'platform_uid' => '', 'profile_url' => ''],
            ],
        ])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('status', 'contact-updated');

    $contact->refresh();

    expect($contact->name)->toBe('New Name')
        ->and($contact->handles->count())->toBe(1)
        ->and($contact->handleFor('facebook')?->handle)->toBe('@new');
});

test('a contact can be deleted', function () {
    $contact = Contact::factory()->create([
        'business_id' => $this->business->getKey(),
    ]);

    $this->actingAs($this->user)
        ->delete(route('contacts.destroy', $contact))
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('status', 'contact-deleted');

    expect(Contact::withoutBusinessScope(fn () => Contact::whereKey($contact->getKey())->exists()))->toBeFalse();
});

test('the search endpoint returns contacts for the editor autocomplete', function () {
    $contact = Contact::factory()->create([
        'business_id' => $this->business->getKey(),
        'name' => 'Jane Doe',
    ]);
    $contact->handles()->create(['platform' => 'instagram', 'handle' => 'janedoe']);

    $this->actingAs($this->user)
        ->getJson(route('contacts.search', ['q' => 'Jane']))
        ->assertOk()
        ->assertJson([
            [
                'id' => (string) $contact->getKey(),
                'value' => 'Jane Doe',
                'name' => 'Jane Doe',
                'handle' => '@janedoe',
            ],
        ]);
});

test('the search endpoint never returns contacts from other businesses', function () {
    $other = Business::factory()->create();
    Contact::factory()->create(['business_id' => $other->getKey(), 'name' => 'Sneaky Sally']);

    $this->actingAs($this->user)
        ->getJson(route('contacts.search', ['q' => 'Sally']))
        ->assertOk()
        ->assertJson([]);
});

test('the contacts module page renders', function () {
    $this->actingAs($this->user)
        ->get(route('contacts.index'))
        ->assertOk();
});

test('contact handle platforms are the mentionable platforms', function () {
    expect(ContactHandle::PLATFORMS)->toBe([
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
    ]);
});
