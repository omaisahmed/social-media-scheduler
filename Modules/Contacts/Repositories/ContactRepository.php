<?php

declare(strict_types=1);

namespace Modules\Contacts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Contacts\Models\Contact;
use Modules\Contacts\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Core\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Contact>
 */
final class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    public function model(): string
    {
        return Contact::class;
    }

    public function forBusiness(int $businessId, array $with = []): Collection
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->get();
    }

    public function paginateForBusiness(int $businessId, array $filters = [], int $perPage = 15, array $with = ['handles']): LengthAwarePaginator
    {
        $query = $this->newQuery()->with($with)->where('business_id', $businessId);

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('handles', fn ($handles) => $handles->where('handle', 'like', '%'.$search.'%'));
            });
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function findForBusiness(int $businessId, int $contactId, array $with = []): ?Contact
    {
        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->whereKey($contactId)
            ->first();
    }

    public function searchForBusiness(int $businessId, string $query, int $limit = 10): Collection
    {
        if (trim($query) === '') {
            return $this->forBusiness($businessId, ['handles'])->take($limit);
        }

        return $this->newQuery()
            ->with('handles')
            ->where('business_id', $businessId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%'.$query.'%')
                    ->orWhereHas('handles', fn ($handles) => $handles->where('handle', 'like', '%'.$query.'%'));
            })
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function findByIds(int $businessId, array $ids, array $with = []): Collection
    {
        if ($ids === []) {
            return new Collection;
        }

        return $this->newQuery()
            ->with($with)
            ->where('business_id', $businessId)
            ->whereIn('id', $ids)
            ->get();
    }

    public function deleteForBusiness(int $businessId, int $contactId): bool
    {
        return (bool) $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($contactId)
            ->delete();
    }
}
