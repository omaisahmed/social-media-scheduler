<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Modules\Contacts\Models\Contact;
use Modules\Posts\Models\Post;
use Modules\Scheduler\Services\FacebookPublisher;
use Modules\SocialAccounts\Models\SocialAccount;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['business_id' => 1, 'email_verified_at' => now()]);
});

function facebookMentionHtml(int $id, string $value = 'Jane Doe'): string
{
    return '<p>Say hi to <span class="ql-mention" data-id="'.$id.'" data-value="'.e($value).'" data-denotation-char="@">@'.$value.'</span></p>';
}

test('facebook publisher sends the graph tag when the contact has a page uid', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response(['id' => 'fb_post_123'], 200),
    ]);

    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);
    $contact->handles()->create(['platform' => 'facebook', 'handle' => '@janedoe', 'platform_uid' => '987654321']);

    $post = Post::factory()->create([
        'business_id' => 1,
        'content' => facebookMentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    $account = SocialAccount::factory()->create([
        'business_id' => 1,
        'platform' => SocialAccount::PLATFORM_FACEBOOK,
        'account_identifier' => '1234567890',
        'access_token' => 'secret-token',
        'is_connected' => true,
    ]);

    $id = app(FacebookPublisher::class)->publish($post, $account);

    expect($id)->toBe('fb_post_123');

    Http::assertSent(function ($request) {
        return str_contains($request['message'] ?? '', '@[987654321:1:Jane Doe]');
    });
});
