<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Modules\Business\Models\Business;
use Modules\Contacts\Models\Contact;
use Modules\SocialAccounts\Models\SocialAccount;

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->user = User::factory()->create([
        'business_id' => $this->business->getKey(),
        'email_verified_at' => now(),
    ]);
});

test('a facebook account can import its page followers as contacts', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'data' => [
                ['id' => '1001', 'name' => 'Follower Page One'],
                ['id' => '1002', 'name' => 'Follower Page Two'],
            ],
        ], 200),
    ]);

    $account = SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'platform' => SocialAccount::PLATFORM_FACEBOOK,
        'account_identifier' => '1234567890',
        'access_token' => 'page-token',
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->post(route('contacts.import'), ['account_id' => $account->getKey()])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('status', 'Imported 2 of 2 followers.');

    $contacts = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->with('handles')->get());

    expect($contacts)->toHaveCount(2)
        ->and($contacts->first()->handles->first()->platform)->toBe('facebook')
        ->and($contacts->first()->handles->first()->platform_uid)->toBe('1001')
        ->and($contacts->first()->name)->toBe('Follower Page One');
});

test('importing the same followers twice does not create duplicates', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'data' => [['id' => '1001', 'name' => 'Follower Page One']],
        ], 200),
    ]);

    $account = SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'platform' => SocialAccount::PLATFORM_FACEBOOK,
        'account_identifier' => '1234567890',
        'access_token' => 'page-token',
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)->post(route('contacts.import'), ['account_id' => $account->getKey()]);
    $this->actingAs($this->user)
        ->post(route('contacts.import'), ['account_id' => $account->getKey()])
        ->assertSessionHas('status', 'Imported 0 of 1 followers.');

    $count = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->count());

    expect($count)->toBe(1);
});

test('a failed import surfaces the platform error', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid OAuth access token.']], 400),
    ]);

    $account = SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'platform' => SocialAccount::PLATFORM_FACEBOOK,
        'account_identifier' => '1234567890',
        'access_token' => 'bad-token',
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->post(route('contacts.import'), ['account_id' => $account->getKey()])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('status', 'Import failed: Invalid OAuth access token.');

    $count = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->count());

    expect($count)->toBe(0);
});

test('import rejects accounts from another business', function () {
    $other = Business::factory()->create();

    $account = SocialAccount::factory()->create([
        'business_id' => $other->getKey(),
        'platform' => SocialAccount::PLATFORM_FACEBOOK,
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->post(route('contacts.import'), ['account_id' => $account->getKey()])
        ->assertNotFound();
});

test('the editor search includes live platform results', function () {
    Http::fake([
        'api.twitter.com/*' => Http::response([
            'data' => [
                ['id' => '555', 'name' => 'Jane Remote', 'username' => 'janeremote'],
            ],
        ], 200),
    ]);

    SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'platform' => SocialAccount::PLATFORM_TWITTER,
        'account_identifier' => '999',
        'access_token' => 'x-token',
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->getJson(route('contacts.search', ['q' => 'Jane']))
        ->assertOk()
        ->assertJson([
            [
                'id' => null,
                'remote' => true,
                'platform' => 'twitter',
                'value' => 'Jane Remote',
                'name' => 'Jane Remote',
                'handle' => '@janeremote',
                'uid' => '555',
            ],
        ]);
});

test('live search degrades gracefully when a platform call fails', function () {
    Http::fake([
        'api.twitter.com/*' => Http::response([], 429),
    ]);

    SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'platform' => SocialAccount::PLATFORM_TWITTER,
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->getJson(route('contacts.search', ['q' => 'Jane']))
        ->assertOk()
        ->assertJson([]);
});

test('a remote mention can be saved as a contact and returns its id', function () {
    $response = $this->actingAs($this->user)
        ->postJson(route('contacts.storeRemote'), [
            'platform' => 'twitter',
            'name' => 'Jane Remote',
            'handle' => 'janeremote',
            'platform_uid' => '555',
        ])
        ->assertOk();

    expect($response->json('id'))->toBeNumeric();

    $contact = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->first());

    expect($contact)->not->toBeNull()
        ->and($contact->name)->toBe('Jane Remote')
        ->and($contact->handleFor('twitter')?->handle)->toBe('janeremote')
        ->and($contact->handleFor('twitter')?->platform_uid)->toBe('555');
});

test('saving a remote mention for an existing platform handle returns the existing contact', function () {
    $existing = Contact::factory()->create(['business_id' => $this->business->getKey(), 'name' => 'Jane Remote']);
    $existing->handles()->create(['platform' => 'twitter', 'handle' => 'janeremote', 'platform_uid' => '555']);

    $response = $this->actingAs($this->user)
        ->postJson(route('contacts.storeRemote'), [
            'platform' => 'twitter',
            'name' => 'Jane Remote',
            'handle' => 'janeremote',
            'platform_uid' => '555',
        ])
        ->assertOk();

    expect((int) $response->json('id'))->toBe($existing->getKey());

    $count = Contact::withoutBusinessScope(fn () => Contact::where('business_id', $this->business->getKey())->count());

    expect($count)->toBe(1);
});
