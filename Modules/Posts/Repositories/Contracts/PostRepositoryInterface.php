<?php

declare(strict_types=1);

namespace Modules\Posts\Repositories\Contracts;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Posts\Models\Post;

/**
 * @extends RepositoryInterface<Post>
 */
interface PostRepositoryInterface extends RepositoryInterface
{
    public function paginateForBusiness(
        int $businessId,
        array $filters = [],
        int $perPage = 15,
        array $with = ['user', 'accounts'],
    ): LengthAwarePaginator;

    public function upcomingForBusiness(int $businessId, array $with = []): Collection;

    public function dueForPublishing(): Collection;

    public function forDateRange(int $businessId, CarbonInterface $from, CarbonInterface $to): Collection;

    public function findForBusiness(int $businessId, int $postId, array $with = []): ?Post;

    public function deleteForBusiness(int $businessId, int $postId): bool;
}
