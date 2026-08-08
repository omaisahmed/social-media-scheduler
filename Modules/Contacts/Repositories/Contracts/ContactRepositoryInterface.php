<?php

declare(strict_types=1);

namespace Modules\Contacts\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Contacts\Models\Contact;
use Modules\Core\Repositories\Contracts\RepositoryInterface;

/**
 * @extends RepositoryInterface<Contact>
 */
interface ContactRepositoryInterface extends RepositoryInterface
{
    public function forBusiness(int $businessId, array $with = []): Collection;

    public function paginateForBusiness(int $businessId, array $filters = [], int $perPage = 15, array $with = ['handles']): LengthAwarePaginator;

    public function findForBusiness(int $businessId, int $contactId, array $with = []): ?Contact;

    public function searchForBusiness(int $businessId, string $query, int $limit = 10): Collection;

    public function findByIds(int $businessId, array $ids, array $with = []): Collection;

    public function deleteForBusiness(int $businessId, int $contactId): bool;
}
