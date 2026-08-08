<?php

declare(strict_types=1);

namespace Modules\Teams\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\Teams\Models\TeamMember;
use Modules\Teams\Repositories\Contracts\TeamMemberRepositoryInterface;

/**
 * @extends BaseRepository<TeamMember>
 */
final class TeamMemberRepository extends BaseRepository implements TeamMemberRepositoryInterface
{
    public function model(): string
    {
        return TeamMember::class;
    }

    public function forBusiness(int $businessId, array $with = []): Collection
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->get();
    }

    public function findForBusiness(int $businessId, int $userId): ?TeamMember
    {
        return $this->newQuery()
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->first();
    }

    public function deleteForBusiness(int $businessId, int $userId): bool
    {
        return (bool) $this->newQuery()
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->delete();
    }
}
