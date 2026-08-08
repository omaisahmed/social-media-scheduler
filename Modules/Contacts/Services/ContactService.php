<?php

declare(strict_types=1);

namespace Modules\Contacts\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\ContactHandle;
use Modules\Contacts\Repositories\Contracts\ContactRepositoryInterface;

final class ContactService
{
    public function __construct(protected ContactRepositoryInterface $contacts) {}

    public function create(int $businessId, array $attributes, array $handles = []): Contact
    {
        return DB::transaction(function () use ($businessId, $attributes, $handles) {
            $contact = $this->contacts->create([
                'business_id' => $businessId,
                'name' => $attributes['name'],
                'avatar_url' => $attributes['avatar_url'] ?? null,
            ]);

            $this->syncHandles($contact, $handles);

            return $contact->fresh('handles');
        });
    }

    public function update(int $businessId, int $contactId, array $attributes, array $handles = []): ?Contact
    {
        $contact = $this->contacts->findForBusiness($businessId, $contactId, ['handles']);

        if (! $contact) {
            return null;
        }

        return DB::transaction(function () use ($contact, $attributes, $handles) {
            $this->contacts->update($contact->getKey(), [
                'name' => $attributes['name'],
                'avatar_url' => $attributes['avatar_url'] ?? null,
            ]);

            $this->syncHandles($contact, $handles);

            return $contact->fresh('handles');
        });
    }

    /**
     * Whether a contact in this business already carries a platform handle
     * matching the given item's uid or handle.
     *
     * @param  array<string, mixed>  $item
     */
    public function hasPlatformHandle(int $businessId, string $platform, array $item): bool
    {
        return $this->platformHandleQuery($businessId, $platform, $item)->exists();
    }

    /**
     * Return the existing contact for a platform item, or create one with
     * its handle. Used by follower imports and live editor search.
     *
     * @param  array<string, mixed>  $item
     */
    public function upsertFromPlatform(int $businessId, string $platform, array $item): ?Contact
    {
        $handle = $this->platformHandleQuery($businessId, $platform, $item)->with('contact')->first();

        if ($handle !== null && $handle->contact !== null) {
            return $handle->contact;
        }

        return $this->create($businessId, [
            'name' => trim((string) ($item['name'] ?? '')) ?: (trim((string) ($item['handle'] ?? '')) ?: 'Contact'),
            'avatar_url' => $item['avatar'] ?? null,
        ], [
            $platform => [
                'handle' => $item['handle'] ?? null,
                'platform_uid' => $item['uid'] ?? null,
                'profile_url' => $item['profile_url'] ?? null,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function platformHandleQuery(int $businessId, string $platform, array $item): Builder
    {
        $uid = trim((string) ($item['uid'] ?? ''));
        $handle = trim((string) ($item['handle'] ?? ''));

        $query = ContactHandle::query()->where('platform', $platform);

        if ($uid !== '') {
            $query->where('platform_uid', $uid);
        } elseif ($handle !== '') {
            $query->where('handle', $handle);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query->whereHas('contact', fn ($q) => $q->where('business_id', $businessId));
    }

    /**
     * Replace a contact's handles with the submitted values.
     *
     * @param  array<string, array{handle?: string|null, platform_uid?: string|null, profile_url?: string|null}>  $handles
     */
    protected function syncHandles(Contact $contact, array $handles): void
    {
        $contact->handles()->delete();

        foreach (ContactHandle::PLATFORMS as $platform) {
            $data = $handles[$platform] ?? [];

            $handle = trim((string) ($data['handle'] ?? ''));
            $uid = trim((string) ($data['platform_uid'] ?? ''));
            $profileUrl = trim((string) ($data['profile_url'] ?? ''));

            if ($handle === '' && $uid === '' && $profileUrl === '') {
                continue;
            }

            $contact->handles()->create([
                'platform' => $platform,
                'handle' => $handle === '' ? null : $handle,
                'platform_uid' => $uid === '' ? null : $uid,
                'profile_url' => $profileUrl === '' ? null : $profileUrl,
            ]);
        }
    }
}
