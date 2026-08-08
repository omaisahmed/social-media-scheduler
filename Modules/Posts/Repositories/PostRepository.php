<?php

declare(strict_types=1);

namespace Modules\Posts\Repositories;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\Posts\Models\Post;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;

/**
 * @extends BaseRepository<Post>
 */
final class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function model(): string
    {
        return Post::class;
    }

    public function paginateForBusiness(
        int $businessId,
        array $filters = [],
        int $perPage = 15,
        array $with = ['user', 'accounts', 'featuredMedia'],
    ): LengthAwarePaginator {
        $query = $this->newQuery()->with($with)->where('business_id', $businessId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%'.$filters['search'].'%')
                    ->orWhere('content', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function upcomingForBusiness(int $businessId, array $with = []): Collection
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->where('status', Post::STATUS_SCHEDULED)
            ->where('scheduled_at', '>=', now()->utc())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();
    }

    public function dueForPublishing(): Collection
    {
        return $this->newQuery()
            ->where('status', Post::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now()->utc())
            ->orderBy('scheduled_at')
            ->get();
    }

    public function forDateRange(int $businessId, CarbonInterface $from, CarbonInterface $to): Collection
    {
        return $this->newQuery()
            ->with(['accounts'])
            ->where('business_id', $businessId)
            ->whereBetween('scheduled_at', [$from->utc(), $to->utc()])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function findForBusiness(int $businessId, int $postId, array $with = []): ?Post
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->whereKey($postId)
            ->first();
    }

    public function deleteForBusiness(int $businessId, int $postId): bool
    {
        return (bool) $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($postId)
            ->delete();
    }
}
