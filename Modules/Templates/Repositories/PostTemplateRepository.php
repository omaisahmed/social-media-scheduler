<?php

declare(strict_types=1);

namespace Modules\Templates\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\BaseRepository;
use Modules\Templates\Models\PostTemplate;
use Modules\Templates\Repositories\Contracts\PostTemplateRepositoryInterface;

/**
 * @extends BaseRepository<PostTemplate>
 */
final class PostTemplateRepository extends BaseRepository implements PostTemplateRepositoryInterface
{
    public function model(): string
    {
        return PostTemplate::class;
    }

    public function paginateForBusiness(int $businessId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->newQuery()->where('business_id', $businessId);

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('content', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function findForBusiness(int $businessId, int $templateId): ?PostTemplate
    {
        return $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($templateId)
            ->first();
    }
}
