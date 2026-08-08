<?php

declare(strict_types=1);

namespace Modules\Contacts\Services;

use Illuminate\Support\Facades\DB;
use Modules\Contacts\Services\Importers\ContactImporterInterface;
use Modules\Contacts\Services\Importers\FacebookContactImporter;
use Modules\Contacts\Services\Importers\InstagramContactImporter;
use Modules\Contacts\Services\Importers\TwitterContactImporter;
use Modules\SocialAccounts\Models\SocialAccount;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;

/**
 * Imports mentionable accounts from connected social accounts and searches
 * the platforms live for the editor's @mention autocomplete.
 */
final class ContactImportService
{
    /** @var array<string, class-string<ContactImporterInterface>> */
    private const IMPORTERS = [
        SocialAccount::PLATFORM_FACEBOOK => FacebookContactImporter::class,
        SocialAccount::PLATFORM_INSTAGRAM => InstagramContactImporter::class,
        SocialAccount::PLATFORM_TWITTER => TwitterContactImporter::class,
    ];

    public function __construct(
        protected ContactService $contacts,
        protected SocialAccountRepositoryInterface $accounts,
    ) {}

    /**
     * @return array{imported: int, total: int, errors: string[]}
     */
    public function importFromAccount(int $businessId, SocialAccount $account): array
    {
        $importer = $this->importerFor($account->platform);

        if ($importer === null) {
            return [
                'imported' => 0,
                'total' => 0,
                'errors' => ["Contact import is not supported for {$account->platform}."],
            ];
        }

        $result = $importer->import($account);

        if ($result->errors !== []) {
            return ['imported' => 0, 'total' => 0, 'errors' => $result->errors];
        }

        $imported = 0;

        DB::transaction(function () use ($businessId, $account, $result, &$imported): void {
            foreach ($result->items as $item) {
                if ($this->contacts->hasPlatformHandle($businessId, $account->platform, $item)) {
                    continue;
                }

                $this->contacts->upsertFromPlatform($businessId, $account->platform, $item);
                $imported++;
            }
        });

        return [
            'imported' => $imported,
            'total' => count($result->items),
            'errors' => [],
        ];
    }

    /**
     * Search connected platforms for mentionable accounts not yet saved
     * locally, so the editor can offer them inline.
     *
     * @return array<int, array{id: null, value: string, name: string, handle: string|null, avatar: string|null, platform: string, uid: string, remote: bool}>
     */
    public function search(int $businessId, string $query, int $perAccount = 6): array
    {
        $query = trim($query);
        $results = [];

        if ($query === '') {
            return $results;
        }

        foreach ($this->accounts->connectedForBusiness($businessId) as $account) {
            $importer = $this->importerFor($account->platform);

            if ($importer === null) {
                continue;
            }

            try {
                foreach ($importer->search($account, $query, $perAccount) as $item) {
                    $handle = isset($item['handle']) && $item['handle'] !== null
                        ? '@'.ltrim($item['handle'], '@')
                        : null;

                    $results[] = [
                        'id' => null,
                        'value' => $item['name'],
                        'name' => $item['name'],
                        'handle' => $handle,
                        'avatar' => $item['avatar'] ?? null,
                        'platform' => $account->platform,
                        'uid' => $item['uid'] ?? '',
                        'remote' => true,
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $results;
    }

    public function importerFor(string $platform): ?ContactImporterInterface
    {
        $class = self::IMPORTERS[$platform] ?? null;

        return $class !== null ? app($class) : null;
    }
}
