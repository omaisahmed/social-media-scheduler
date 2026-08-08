<?php

declare(strict_types=1);

namespace Modules\Contacts\Services\Importers;

use Illuminate\Support\Facades\Http;
use Modules\SocialAccounts\Models\SocialAccount;

/**
 * Discovers X / Twitter users through the v2 API using the account's
 * bearer or user token. Mentions are encoded as @handle, and the numeric
 * user id is stored so future rich-mention formats can be supported.
 */
final class TwitterContactImporter implements ContactImporterInterface
{
    public function platform(): string
    {
        return SocialAccount::PLATFORM_TWITTER;
    }

    public function import(SocialAccount $account, int $limit = 500): ImportResult
    {
        $userId = trim((string) $account->account_identifier);
        $token = (string) $account->access_token;

        if ($userId === '' || $token === '') {
            return new ImportResult([], ['X account is missing its user id or access token. Reconnect it first.']);
        }

        $response = Http::withToken($token)
            ->get($this->url("/users/{$userId}/followers"), [
                'max_results' => min($limit, 1000),
                'user.fields' => 'name,username,profile_image_url',
            ]);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || ! isset($data['data'])) {
            $error = is_array($data)
                ? ($data['errors'][0]['message'] ?? 'X followers request failed.')
                : 'X followers request failed.';

            return new ImportResult([], [$error]);
        }

        $items = array_values(array_filter(array_map(
            static fn ($user) => is_array($user) && isset($user['id'])
                ? self::normalize($user)
                : null,
            $data['data'],
        )));

        return new ImportResult($items);
    }

    public function search(SocialAccount $account, string $query, int $limit = 6): array
    {
        $query = trim($query);
        $token = (string) $account->access_token;

        if ($query === '' || $token === '') {
            return [];
        }

        $response = Http::withToken($token)
            ->get($this->url('/users/search'), [
                'query' => $query,
                'max_results' => min($limit, 10),
                'user.fields' => 'name,username,profile_image_url',
            ]);

        if ($response->failed()) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($user) => is_array($user) && isset($user['id'])
                ? self::normalize($user)
                : null,
            $response->json('data') ?? [],
        )));
    }

    /**
     * @param  array{id: string|int, name?: string|null, username?: string|null, profile_image_url?: string|null}  $user
     * @return array{name: string, handle: string, uid: string, avatar: string|null, profile_url: string}
     */
    private static function normalize(array $user): array
    {
        $id = (string) $user['id'];
        $username = trim((string) ($user['username'] ?? ''));

        return [
            'name' => trim((string) ($user['name'] ?? '')) ?: ($username ?: $id),
            'handle' => $username ?: null,
            'uid' => $id,
            'avatar' => $user['profile_image_url'] ?? null,
            'profile_url' => $username !== '' ? "https://x.com/{$username}" : null,
        ];
    }

    private function url(string $path): string
    {
        return 'https://api.twitter.com/2'.$path;
    }
}
