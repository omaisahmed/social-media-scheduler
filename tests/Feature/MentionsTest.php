<?php

declare(strict_types=1);

use Modules\Contacts\Models\Contact;
use Modules\Posts\Models\Post;
use Modules\Posts\Services\PostMessageBuilder;

function mentionHtml(int $id, string $value = 'Jane Doe'): string
{
    return '<p>Say hi to <span class="ql-mention" data-id="'.$id.'" data-value="'.e($value).'" data-denotation-char="@">@'.$value.'</span>!</p>';
}

test('facebook message uses the graph tag syntax when the contact has a facebook uid', function () {
    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);
    $contact->handles()->create(['platform' => 'facebook', 'handle' => '@janedoe', 'platform_uid' => '987654321']);

    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'facebook'))->toBe('Say hi to @[987654321:1:Jane Doe]!');
});

test('instagram message uses the raw @handle', function () {
    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);
    $contact->handles()->create(['platform' => 'instagram', 'handle' => 'janedoe']);

    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'instagram'))->toBe('Say hi to @janedoe!');
});

test('twitter message uses the raw @handle', function () {
    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);
    $contact->handles()->create(['platform' => 'twitter', 'handle' => 'janedoe']);

    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'twitter'))->toBe('Say hi to @janedoe!');
});

test('linkedin message uses the little-format urn mention', function () {
    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);
    $contact->handles()->create(['platform' => 'linkedin', 'handle' => 'janedoe', 'platform_uid' => 'urn:li:person:abc123']);

    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'linkedin'))->toBe('Say hi to @[Jane Doe](urn:li:person:abc123)!');
});

test('message falls back to plain @name when the contact has no matching handle', function () {
    $contact = Contact::factory()->create(['business_id' => 1, 'name' => 'Jane Doe']);

    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml($contact->getKey(), 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'twitter'))->toBe('Say hi to @Jane Doe!');
});

test('message keeps plain text when the mention contact no longer exists', function () {
    $post = new Post([
        'business_id' => 1,
        'content' => mentionHtml(999999, 'Jane Doe'),
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'facebook'))->toBe('Say hi to @Jane Doe!');
});

test('plain mentions without a matching contact handle stay readable', function () {
    $post = new Post([
        'business_id' => 1,
        'content' => '<p>Hi <span class="ql-mention" data-id="1" data-value="John" data-denotation-char="@">@John</span>, welcome.</p>',
    ]);

    expect(app(PostMessageBuilder::class)->build($post, 'facebook'))->toBe('Hi @John, welcome.');
});

test('post reports its mentions from the rich text content', function () {
    $post = new Post([
        'content' => '<p><span class="ql-mention" data-id="7" data-value="Jane" data-denotation-char="@">@Jane</span> and <span class="ql-mention" data-id="9" data-value="John" data-denotation-char="@">@John</span>.</p>',
    ]);

    expect($post->mentions())->toBe([
        ['id' => 7, 'value' => 'Jane', 'denotation_char' => '@'],
        ['id' => 9, 'value' => 'John', 'denotation_char' => '@'],
    ]);
});
