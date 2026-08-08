<?php

declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\Contracts\RepositoryInterface;

/**
 * Generic, reusable repository implementation.
 *
 * Extend this class and supply the model class to get standard CRUD
 * operations. The fluent `query()` helper exposes the underlying query
 * builder for advanced, module-specific queries.
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct()
    {
    }

    /**
     * The fully qualified model class managed by this repository.
     *
     * @return class-string<Model>
     */
    abstract public function model(): string;

    protected function newQuery(): Builder
    {
        return app($this->model())->newQuery();
    }

    public function find(int|string $id, array $with = []): ?Model
    {
        return $this->newQuery()->with($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = []): Model
    {
        return $this->newQuery()->with($with)->findOrFail($id);
    }

    public function all(array $with = []): Collection
    {
        return $this->newQuery()->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = [], array $filters = []): LengthAwarePaginator
    {
        $query = $this->newQuery()->with($with);

        foreach ($filters as $column => $value) {
            if ($value === null || $value === '' || is_array($value) && empty($value)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function create(array $attributes): Model
    {
        return $this->newQuery()->create($attributes);
    }

    public function update(int|string $id, array $attributes): Model
    {
        $model = $this->findOrFail($id);
        $model->update($attributes);

        return $model->fresh();
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    public function firstWhere(string $column, mixed $value): ?Model
    {
        return $this->newQuery()->where($column, $value)->first();
    }
}
