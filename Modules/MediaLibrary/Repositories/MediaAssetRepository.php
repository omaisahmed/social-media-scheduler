<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\BaseRepository;
use Modules\MediaLibrary\Models\MediaAsset;
use Modules\MediaLibrary\Repositories\Contracts\MediaAssetRepositoryInterface;

/**
 * @extends BaseRepository<MediaAsset>
 */
final class MediaAssetRepository extends BaseRepository implements MediaAssetRepositoryInterface
{
    public function model(): string
    {
        return MediaAsset::class;
    }

    public function paginateForBusiness(int $businessId, int $perPage = 24): LengthAwarePaginator
    {
        return $this->newQuery()
            ->where('business_id', $businessId)
            ->latest('id')
            ->paginate($perPage);
    }

    public function findForBusiness(int $businessId, int $assetId): ?MediaAsset
    {
        return $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($assetId)
            ->first();
    }

    public function deleteForBusiness(int $businessId, int $assetId): bool
    {
        return (bool) $this->newQuery()
            ->where('business_id', $businessId)
            ->whereKey($assetId)
            ->delete();
    }
}
