<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contract every repository implements so services can depend on an
 * abstraction instead of a concrete repository.
 */
interface RepositoryInterface
{
    public function find(int|string $id, array $with = []): ?Model;

    public function findOrFail(int|string $id, array $with = []): Model;

    public function all(array $with = []): Collection;

    public function paginate(int $perPage = 15, array $with = [], array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Model;

    public function update(int|string $id, array $attributes): Model;

    public function delete(int|string $id): bool;

    public function firstWhere(string $column, mixed $value): ?Model;
}
