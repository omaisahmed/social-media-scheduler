<?php

declare(strict_types=1);

namespace Modules\Teams\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\Teams\Models\TeamInvitation;
use Modules\Teams\Repositories\Contracts\TeamInvitationRepositoryInterface;

/**
 * @extends BaseRepository<TeamInvitation>
 */
final class TeamInvitationRepository extends BaseRepository implements TeamInvitationRepositoryInterface
{
    public function model(): string
    {
        return TeamInvitation::class;
    }

    public function pendingForBusiness(int $businessId, array $with = []): Collection
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->get();
    }

    public function findByToken(string $token): ?TeamInvitation
    {
        return $this->firstWhere('token', $token);
    }
}
