<?php

declare(strict_types=1);

namespace Modules\Templates\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Templates\Models\PostTemplate;

/**
 * @extends RepositoryInterface<PostTemplate>
 */
interface PostTemplateRepositoryInterface extends RepositoryInterface
{
    public function paginateForBusiness(int $businessId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findForBusiness(int $businessId, int $templateId): ?PostTemplate;
}
