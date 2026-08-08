<?php

declare(strict_types=1);

namespace Modules\Templates\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Templates\Repositories\Contracts\PostTemplateRepositoryInterface;
use Modules\Templates\Repositories\PostTemplateRepository;

final class TemplatesServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'templates';

    protected string $moduleNamespace = 'templates';

    public function register(): void
    {
        parent::register();

        $this->app->bind(PostTemplateRepositoryInterface::class, PostTemplateRepository::class);
    }
}
