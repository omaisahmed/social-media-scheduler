<?php

declare(strict_types=1);

namespace Modules\Business\Repositories;

use Modules\Business\Models\Business;
use Modules\Business\Repositories\Contracts\BusinessRepositoryInterface;
use Modules\Core\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Business>
 */
final class BusinessRepository extends BaseRepository implements BusinessRepositoryInterface
{
    public function model(): string
    {
        return Business::class;
    }

    public function findBySlug(string $slug): ?Business
    {
        return $this->query()->where('slug', $slug)->first();
    }
}
