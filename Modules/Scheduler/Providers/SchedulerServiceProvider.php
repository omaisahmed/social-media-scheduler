<?php

declare(strict_types=1);

namespace Modules\Scheduler\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Scheduler\Console\PublishDuePostsCommand;

final class SchedulerServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'scheduler';

    protected string $moduleNamespace = 'scheduler';

    public function boot(): void
    {
        parent::boot();

        $this->commands([
            PublishDuePostsCommand::class,
        ]);
    }
}
