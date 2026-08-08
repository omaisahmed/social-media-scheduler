<?php

declare(strict_types=1);

namespace Modules\Posts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\BaseRepository;
use Modules\Posts\Models\PostAccount;
use Modules\Posts\Repositories\Contracts\PostAccountRepositoryInterface;

/**
 * @extends BaseRepository<PostAccount>
 */
final class PostAccountRepository extends BaseRepository implements PostAccountRepositoryInterface
{
    public function model(): string
    {
        return PostAccount::class;
    }

    public function forPost(int $postId): Collection
    {
        return $this->newQuery()
            ->where('post_id', $postId)
            ->get();
    }
}
