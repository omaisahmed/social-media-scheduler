<?php

declare(strict_types=1);

namespace Modules\Contacts\Services\Importers;

use Modules\SocialAccounts\Models\SocialAccount;

/**
 * Discovers mentionable accounts on a social platform through its official API.
 */
interface ContactImporterInterface
{
    public function platform(): string;

    /**
     * Fetch accounts that can be mentioned from the connected account
     * (typically its followers) and return them as normalized items.
     */
    public function import(SocialAccount $account, int $limit = 500): ImportResult;

    /**
     * Search the platform for accounts matching the query. Returns
     * normalized items; failures should degrade to an empty array.
     *
     * @return array<int, array{name: string, handle?: string|null, uid?: string|null, avatar?: string|null, profile_url?: string|null}>
     */
    public function search(SocialAccount $account, string $query, int $limit = 6): array;
}
