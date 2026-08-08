<?php

declare(strict_types=1);

namespace Modules\Contacts\Services\Importers;

use Illuminate\Support\Facades\Http;
use Modules\SocialAccounts\Models\SocialAccount;

/**
 * Discovers Facebook Pages through the Graph API using the account's
 * Page access token. Mentions are encoded as @[pageId:1:Name], so the
 * numeric page id is stored as the platform_uid.
 */
final class FacebookContactImporter implements ContactImporterInterface
{
    public function platform(): string
    {
        return SocialAccount::PLATFORM_FACEBOOK;
    }

    public function import(SocialAccount $account, int $limit = 500): ImportResult
    {
        $pageId = trim((string) $account->account_identifier);
        $token = (string) $account->access_token;

        if ($pageId === '' || $token === '') {
            return new ImportResult([], ['Facebook account is missing a Page ID or access token. Reconnect it first.']);
        }

        $response = Http::get($this->url("/{$pageId}/followers"), [
            'access_token' => $token,
            'fields' => 'id,name',
            'limit' => $limit,
        ]);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || ! isset($data['data'])) {
            $error = is_array($data)
                ? ($data['error']['message'] ?? 'Facebook followers request failed.')
                : 'Facebook followers request failed.';

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
        $query = trim($query);
        $token = (string) $account->access_token;

        if ($query === '' || $token === '') {
            return [];
        }

        $response = Http::get($this->url('/search'), [
            'access_token' => $token,
            'type' => 'page',
            'q' => $query,
            'fields' => 'id,name,picture',
            'limit' => $limit,
        ]);

        $data = $response->json();

        if ($response->failed() || ! is_array($data) || ! isset($data['data'])) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($page) => is_array($page) && isset($page['id'])
                ? self::normalize($page)
                : null,
            $data['data'],
        )));
    }

    /**
     * @param  array{id: string|int, name?: string|null, picture?: array{picture?: array{url?: string}}|null}  $follower
     * @return array{name: string, handle: null, uid: string, avatar: string|null, profile_url: string}
     */
    private static function normalize(array $follower): array
    {
        $id = (string) $follower['id'];

        return [
            'name' => trim((string) ($follower['name'] ?? '')) ?: $id,
            'handle' => null,
            'uid' => $id,
            'avatar' => $follower['picture']['data']['url'] ?? null,
            'profile_url' => "https://facebook.com/{$id}",
        ];
    }

    private function url(string $path): string
    {
        return 'https://graph.facebook.com/'.config('services.facebook.graph_version').$path;
    }
}
