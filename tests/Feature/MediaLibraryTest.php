<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Business\Models\Business;
use Modules\MediaLibrary\Models\MediaAsset;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->business = Business::factory()->create();
    $this->user = User::factory()->create([
        'business_id' => $this->business->getKey(),
        'email_verified_at' => now(),
    ]);
});

test('a user can upload a media file', function () {
    $file = UploadedFile::fake()->image('blog-banner-07.png', 1920, 1280);

    $this->actingAs($this->user)
        ->post(route('media.store'), ['files' => [$file]])
        ->assertRedirect()
        ->assertSessionHas('status', 'media-uploaded');

    $asset = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $this->business->getKey())->first());

    expect($asset)->not->toBeNull()
        ->and($asset->original_name)->toBe('blog-banner-07.png')
        ->and($asset->type)->toBe(MediaAsset::TYPE_IMAGE)
        ->and($asset->width)->toBe(1920)
        ->and($asset->height)->toBe(1280);

    Storage::disk('public')->assertExists($asset->path);
    Storage::disk('public')->assertExists($asset->thumb_path);

    $this->actingAs($this->user)
        ->get(route('media.index'))
        ->assertOk()
        ->assertSee('blog-banner-07.png');
});

test('a thumbnail is generated for image uploads', function () {
    $file = UploadedFile::fake()->image('thumb-check.png', 1920, 1280);

    $this->actingAs($this->user)
        ->post(route('media.store'), ['files' => [$file]])
        ->assertRedirect()
        ->assertSessionHas('status', 'media-uploaded');

    $asset = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $this->business->getKey())->first());

    expect($asset)->not->toBeNull()
        ->and($asset->thumb_path)->not->toBeNull()
        ->and(Storage::disk('public')->exists($asset->thumb_path))->toBeTrue();

    $thumbInfo = getimagesizefromstring(Storage::disk('public')->get($asset->thumb_path));
    expect($thumbInfo)->not->toBeNull()
        ->and($thumbInfo[0])->toBeLessThanOrEqual(400)
        ->and($thumbInfo[1])->toBeLessThanOrEqual(300);

    $this->actingAs($this->user)
        ->delete(route('media.destroy', $asset))
        ->assertRedirect();

    Storage::disk('public')->assertMissing($asset->path);
    Storage::disk('public')->assertMissing($asset->thumb_path);
});

test('deleting a media file removes it from storage', function () {
    $file = UploadedFile::fake()->image('delete-me.png', 800, 600);

    $this->actingAs($this->user)
        ->post(route('media.store'), ['files' => [$file]]);

    $asset = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $this->business->getKey())->first());
    Storage::disk('public')->assertExists($asset->path);

    $this->actingAs($this->user)
        ->delete(route('media.destroy', $asset))
        ->assertRedirect()
        ->assertSessionHas('status', 'media-deleted');

    Storage::disk('public')->assertMissing($asset->path);
    expect(MediaAsset::withoutBusinessScope(fn () => MediaAsset::find($asset->getKey())))->toBeNull();
});

test('a user cannot delete another business media file', function () {
    $otherBusiness = Business::factory()->create();
    $otherUser = User::factory()->create(['business_id' => $otherBusiness->getKey()]);
    $file = UploadedFile::fake()->image('other.png', 800, 600);

    $this->actingAs($otherUser)
        ->post(route('media.store'), ['files' => [$file]]);

    $asset = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $otherBusiness->getKey())->first());

    $this->actingAs($this->user)
        ->delete(route('media.destroy', $asset))
        ->assertNotFound();

    Storage::disk('public')->assertExists($asset->path);
});

test('a user can bulk delete selected media files and their storage', function () {
    $this->actingAs($this->user)
        ->post(route('media.store'), ['files' => [
            UploadedFile::fake()->image('one.png', 800, 600),
            UploadedFile::fake()->image('two.png', 800, 600),
            UploadedFile::fake()->image('three.png', 800, 600),
        ]])
        ->assertSessionHas('status', 'media-uploaded');

    $assets = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $this->business->getKey())->get());
    expect($assets)->toHaveCount(3);

    $toDelete = $assets->take(2)->pluck('id')->all();
    $keep = $assets->last();

    $this->actingAs($this->user)
        ->post(route('media.bulk-destroy'), ['assets' => $toDelete])
        ->assertRedirect()
        ->assertSessionHas('status', 'media-deleted');

    foreach ($assets->take(2) as $asset) {
        Storage::disk('public')->assertMissing($asset->path);
        expect(MediaAsset::withoutBusinessScope(fn () => MediaAsset::find($asset->getKey())))->toBeNull();
    }

    Storage::disk('public')->assertExists($keep->path);
    expect(MediaAsset::withoutBusinessScope(fn () => MediaAsset::find($keep->getKey())))->not->toBeNull();
});

test('bulk delete ignores assets from other businesses', function () {
    $otherBusiness = Business::factory()->create();
    $otherUser = User::factory()->create(['business_id' => $otherBusiness->getKey()]);

    $this->actingAs($this->user)
        ->post(route('media.store'), ['files' => [UploadedFile::fake()->image('mine.png', 800, 600)]]);

    $this->actingAs($otherUser)
        ->post(route('media.store'), ['files' => [UploadedFile::fake()->image('theirs.png', 800, 600)]]);

    $mine = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $this->business->getKey())->first());
    $theirs = MediaAsset::withoutBusinessScope(fn () => MediaAsset::where('business_id', $otherBusiness->getKey())->first());

    $this->actingAs($this->user)
        ->post(route('media.bulk-destroy'), ['assets' => [$mine->getKey(), $theirs->getKey()]])
        ->assertRedirect();

    Storage::disk('public')->assertMissing($mine->path);
    Storage::disk('public')->assertExists($theirs->path);
    expect(MediaAsset::withoutBusinessScope(fn () => MediaAsset::find($theirs->getKey())))->not->toBeNull();
});
