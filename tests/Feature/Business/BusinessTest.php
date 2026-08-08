<?php

declare(strict_types=1);

use App\Models\User;
use Modules\Business\Models\Business;

test('an authenticated user can create a business', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('business.store'), [
            'name' => 'Acme Inc',
            'website_url' => 'https://acme.test',
            'industry' => 'SaaS',
            'description' => 'A great SaaS company',
        ])
        ->assertRedirect(route('business.index'))
        ->assertSessionHas('status', 'business-created');

    $business = Business::where('slug', 'acme-inc')->first();

    expect($business)->not->toBeNull()
        ->and($business->name)->toBe('Acme Inc')
        ->and($user->fresh()->business_id)->toBe($business->getKey());
});

test('a user can view the business index', function () {
    $user = User::factory()->create();
    $business = Business::factory()->create();

    $this->actingAs($user)
        ->get(route('business.index'))
        ->assertOk()
        ->assertSee($business->name);
});

test('a user can edit their primary business', function () {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $user->update(['business_id' => $business->getKey()]);

    $this->actingAs($user)
        ->patch(route('business.update', $business), [
            'name' => 'Renamed Business',
        ])
        ->assertRedirect(route('business.edit', $business))
        ->assertSessionHas('status', 'business-updated');

    expect($business->fresh()->name)->toBe('Renamed Business');
});

test('a user cannot edit a business they do not belong to', function () {
    $user = User::factory()->create();
    $otherBusiness = Business::factory()->create();

    $this->actingAs($user)
        ->patch(route('business.update', $otherBusiness), [
            'name' => 'Nope',
        ])
        ->assertForbidden();
});

test('the onboarding screen renders for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('onboarding.business'))
        ->assertOk();
});

test('onboarding stores the business and links the owner', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('onboarding.business.store'), [
            'name' => 'My Startup',
        ])
        ->assertRedirect();

    $business = Business::where('slug', 'my-startup')->first();

    expect($business)->not->toBeNull()
        ->and($user->fresh()->business_id)->toBe($business->getKey());
});
