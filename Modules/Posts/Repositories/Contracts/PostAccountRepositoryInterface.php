<?php

declare(strict_types=1);

namespace Modules\Posts\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Posts\Models\PostAccount;

/**
 * @extends RepositoryInterface<PostAccount>
 */
interface PostAccountRepositoryInterface extends RepositoryInterface
{
    public function forPost(int $postId): Collection;
}
