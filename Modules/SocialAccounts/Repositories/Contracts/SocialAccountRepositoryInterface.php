<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\SocialAccounts\Models\SocialAccount;

/**
 * @extends RepositoryInterface<SocialAccount>
 */
interface SocialAccountRepositoryInterface extends RepositoryInterface
{
    public function connectedForBusiness(int $businessId, array $with = []): Collection;

    public function findForBusiness(int $businessId, int $accountId): ?SocialAccount;

    public function deleteForBusiness(int $businessId, int $accountId): bool;
}
