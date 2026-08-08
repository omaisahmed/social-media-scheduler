<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('redesigned pages render', function () {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->post(route('business.store'), ['name' => 'Smoke Inc']);

    $business = \Modules\Business\Models\Business::where('slug', 'smoke-inc')->first();
    $user->update(['business_id' => $business->getKey()]);

    $template = \Modules\Templates\Models\PostTemplate::factory()->create([
        'business_id' => $business->getKey(),
    ]);

    foreach ([
        'onboarding.next' => route('onboarding.next'),
        'business.edit' => route('business.edit', $business),
        'business.create' => route('business.create'),
        'posts.create' => route('posts.create'),
        'templates.create' => route('templates.create'),
        'templates.edit' => route('templates.edit', $template),
        'profile.edit' => route('profile.edit'),
    ] as $label => $url) {
        $this->get($url)->assertOk();
    }

    $this->get(route('login'))->assertRedirect();
    $this->get(route('register'))->assertRedirect();
});
