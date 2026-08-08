<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Audit\Models\AuditLog;
use Modules\Business\Models\Business;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\Posts\Models\Post;
use Modules\Scheduler\Models\BestTimeWindow;
use Modules\SocialAccounts\Models\SocialAccount;
use Modules\Templates\Models\PostTemplate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->business = Business::factory()->create();
    $this->user = User::factory()->create([
        'business_id' => $this->business->getKey(),
        'email_verified_at' => now(),
    ]);
});

test('module pages render for an authenticated user', function () {
    $routes = [
        'dashboard.index',
        'posts.index',
        'posts.create',
        'calendar.index',
        'scheduler.index',
        'media.index',
        'templates.index',
        'templates.create',
        'ai.index',
        'analytics.index',
        'reports.index',
        'social-accounts.index',
        'contacts.index',
        'contacts.create',
        'teams.index',
        'notifications.index',
        'audit.index',
        'settings.index',
        'business.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($this->user)->get(route($route))->assertOk();
    }
});

test('checkbox forms do not leak blade directives into alpine expressions', function () {
    $accounts = SocialAccount::factory()->count(2)->create([
        'business_id' => $this->business->getKey(),
        'is_connected' => true,
    ]);

    $post = Post::factory()->create([
        'business_id' => $this->business->getKey(),
        'scheduled_at' => now()->addDay(),
    ]);

    foreach (['posts.create', 'settings.index'] as $route) {
        $response = $this->actingAs($this->user)->get(route($route));
        $response->assertOk();
        expect($response->getContent())->not->toContain('@checked');
    }

    $response = $this->actingAs($this->user)->get(route('posts.edit', $post));
    $response->assertOk();
    expect($response->getContent())->not->toContain('@checked');
});

test('a scheduled post can be created and appears on the calendar', function () {
    $account = SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'is_connected' => true,
    ]);

    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => 'Launch day',
            'content' => 'We are launching today!',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'account_ids' => [$account->getKey()],
        ])
        ->assertRedirect();

    $post = Post::withoutBusinessScope(fn () => Post::where('business_id', $this->business->getKey())->first());

    expect($post)->not->toBeNull()
        ->and($post->title)->toBe('Launch day')
        ->and($post->status)->toBe(Post::STATUS_SCHEDULED);

    $this->get(route('calendar.index'))->assertOk();
});

test('a post can be created with string account ids, hashtags and a featured image', function () {
    $account = SocialAccount::factory()->create([
        'business_id' => $this->business->getKey(),
        'is_connected' => true,
    ]);

    $media = MediaAsset::factory()->create([
        'business_id' => $this->business->getKey(),
    ]);

    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => 'Career update',
            'content' => 'Excited to share my next chapter.',
            'hashtags' => '#Maqaam #DigitalTransformation',
            'featured_media_id' => $media->getKey(),
            'account_ids' => [(string) $account->getKey()],
        ])
        ->assertRedirect(route('posts.edit', 1));

    $post = Post::withoutBusinessScope(fn () => Post::where('business_id', $this->business->getKey())->first());

    expect($post)->not->toBeNull()
        ->and($post->hashtags)->toBe('#Maqaam #DigitalTransformation')
        ->and($post->featured_media_id)->toBe((int) $media->getKey())
        ->and($post->hashtagList())->toBe(['#Maqaam', '#DigitalTransformation'])
        ->and($post->accounts->pluck('social_account_id')->map(fn ($id) => (int) $id)->all())->toBe([(int) $account->getKey()])
        ->and($post->accounts->first()->platform)->toBe($account->platform);

    $this->actingAs($this->user)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('#Maqaam')
        ->assertSee('#DigitalTransformation');
});

test('a best time window can be saved in the scheduler', function () {
    $this->actingAs($this->user)
        ->post(route('scheduler.store'), [
            'platform' => 'instagram',
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'score' => 90,
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'best-time-saved');

    $window = BestTimeWindow::withoutBusinessScope(fn () => BestTimeWindow::where('business_id', $this->business->getKey())->first());

    expect($window)->not->toBeNull()
        ->and($window->platform)->toBe('instagram');
});

test('a template can be created and listed', function () {
    $this->actingAs($this->user)
        ->post(route('templates.store'), [
            'name' => 'Product promo',
            'content' => 'Check out our new product!',
            'tags' => 'promo, launch',
        ])
        ->assertRedirect(route('templates.index'));

    $template = PostTemplate::withoutBusinessScope(fn () => PostTemplate::where('business_id', $this->business->getKey())->first());

    expect($template)->not->toBeNull()
        ->and($template->tags)->toBe(['promo', 'launch']);

    $this->actingAs($this->user)
        ->get(route('templates.index'))
        ->assertOk()
        ->assertSee('Product promo');
});

test('audit logs are recorded for tenant scoped models', function () {
    $this->actingAs($this->user)
        ->post(route('templates.store'), [
            'name' => 'Audited template',
            'content' => 'Some content',
        ]);

    $template = PostTemplate::withoutBusinessScope(fn () => PostTemplate::where('business_id', $this->business->getKey())->first());

    $audit = AuditLog::where('business_id', $this->business->getKey())
        ->where('event', 'saved')
        ->where('auditable_type', PostTemplate::class)
        ->first();

    expect($audit)->not->toBeNull()
        ->and($audit->auditable_id)->toBe((int) $template->getKey());
});

test('settings can be updated', function () {
    $this->actingAs($this->user)
        ->post(route('settings.business'), [
            'name' => 'Renamed Workspace',
            'timezone' => 'America/New_York',
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'business-updated');

    expect($this->business->fresh()->name)->toBe('Renamed Workspace');
});
