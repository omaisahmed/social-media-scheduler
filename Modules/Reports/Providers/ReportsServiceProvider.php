<?php

declare(strict_types=1);

namespace Modules\Reports\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Reports\Services\ReportsService;

final class ReportsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'reports';

    protected string $moduleNamespace = 'reports';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(ReportsService::class);
    }
}
