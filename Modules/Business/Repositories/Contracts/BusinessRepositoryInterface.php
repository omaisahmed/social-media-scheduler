<?php

declare(strict_types=1);

namespace Modules\Business\Repositories\Contracts;

use Modules\Business\Models\Business;
use Modules\Core\Repositories\Contracts\RepositoryInterface;

/**
 * @extends RepositoryInterface<Business>
 */
interface BusinessRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Business;
}
