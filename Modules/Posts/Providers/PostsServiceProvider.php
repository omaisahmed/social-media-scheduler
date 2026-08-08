<?php

declare(strict_types=1);

namespace Modules\Posts\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Posts\Repositories\Contracts\PostAccountRepositoryInterface;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;
use Modules\Posts\Repositories\PostAccountRepository;
use Modules\Posts\Repositories\PostRepository;

final class PostsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'posts';

    protected string $moduleNamespace = 'posts';

    public function register(): void
    {
        parent::register();

        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PostAccountRepositoryInterface::class, PostAccountRepository::class);
    }
}
