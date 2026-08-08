<?php

declare(strict_types=1);

namespace Modules\Contacts\Services\Importers;

use Illuminate\Support\Facades\Http;
use Modules\SocialAccounts\Models\SocialAccount;

/**
 * Discovers Instagram Business followers through the Instagram Graph API.
 * Instagram offers no open endpoint to search arbitrary accounts for
 * tagging, so live search is intentionally not supported here.
 */
final class InstagramContactImporter implements ContactImporterInterface
{
    public function platform(): string
    {
        return SocialAccount::PLATFORM_INSTAGRAM;
    }

    public function import(SocialAccount $account, int $limit = 500): ImportResult
    {
        $userId = trim((string) ($account->metadata['instagram_user_id'] ?? $account->account_identifier));
        $token = (string) $account->access_token;

        if ($userId === '' || $token === '') {
            return new ImportResult([], ['Instagram account is missing its user id or access token. Reconnect it first.']);
        }

        $response = Http::get($this->url("/{$userId}/followers"), [
            'access_token' => $token,
            'fields' => 'id,username',
            'limit' => $limit,
        ]);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || ! isset($data['data'])) {
            $error = is_array($data)
                ? ($data['error']['message'] ?? 'Instagram followers request failed.')
                : 'Instagram followers request failed.';

            return new ImportResult([], [$error]);
        }

        $items = array_values(array_filter(array_map(
            static fn ($follower) => is_array($follower) && isset($follower['id'])
                ? self::normalize($follower)
                : null,
            $data['data'],
        )));

        return new ImportResult($items);
    }

    public function search(SocialAccount $account, string $query, int $limit = 6): array
    {
        return [];
    }

    /**
     * @param  array{id: string|int, username?: string|null}  $follower
     * @return array{name: string, handle: string, uid: string, avatar: null, profile_url: string}
     */
    private static function normalize(array $follower): array
    {
        $id = (string) $follower['id'];
        $username = trim((string) ($follower['username'] ?? ''));

        return [
            'name' => $username ?: $id,
            'handle' => $username ?: null,
            'uid' => $id,
            'avatar' => null,
            'profile_url' => $username !== '' ? "https://instagram.com/{$username}" : null,
        ];
    }

    private function url(string $path): string
    {
        return 'https://graph.facebook.com/'.config('services.facebook.graph_version').$path;
    }
}
