<?php

declare(strict_types=1);

namespace Modules\Teams\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Teams\Models\TeamInvitation;

/**
 * @extends RepositoryInterface<TeamInvitation>
 */
interface TeamInvitationRepositoryInterface extends RepositoryInterface
{
    public function pendingForBusiness(int $businessId, array $with = []): Collection;

    public function findByToken(string $token): ?TeamInvitation;
}
