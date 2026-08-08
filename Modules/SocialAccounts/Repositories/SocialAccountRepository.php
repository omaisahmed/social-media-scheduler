<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\SocialAccounts\Models\SocialAccount;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;

/**
 * @extends BaseRepository<SocialAccount>
 */
final class SocialAccountRepository extends BaseRepository implements SocialAccountRepositoryInterface
{
    public function model(): string
    {
        return SocialAccount::class;
    }

    public function connectedForBusiness(int $businessId, array $with = []): Collection
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->where('is_connected', true)
            ->get();
    }

    public function findForBusiness(int $businessId, int $accountId): ?SocialAccount
    {
        return $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($accountId)
            ->first();
    }

    public function deleteForBusiness(int $businessId, int $accountId): bool
    {
        return (bool) $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($accountId)
            ->delete();
    }
}
