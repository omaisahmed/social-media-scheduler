<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\MediaLibrary\Models\MediaAsset;

/**
 * @extends RepositoryInterface<MediaAsset>
 */
interface MediaAssetRepositoryInterface extends RepositoryInterface
{
    public function paginateForBusiness(int $businessId, int $perPage = 24): LengthAwarePaginator;

    public function findForBusiness(int $businessId, int $assetId): ?MediaAsset;

    public function deleteForBusiness(int $businessId, int $assetId): bool;
}
