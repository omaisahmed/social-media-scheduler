<?php

declare(strict_types=1);

namespace Modules\Teams\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Teams\Models\TeamMember;

/**
 * @extends RepositoryInterface<TeamMember>
 */
interface TeamMemberRepositoryInterface extends RepositoryInterface
{
    public function forBusiness(int $businessId, array $with = []): Collection;

    public function findForBusiness(int $businessId, int $userId): ?TeamMember;

    public function deleteForBusiness(int $businessId, int $userId): bool;
}
