<?php

declare(strict_types=1);

namespace Modules\Analytics\Providers;

use Modules\Analytics\Services\AnalyticsService;
use Modules\Core\Providers\ModuleServiceProvider;

final class AnalyticsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'analytics';

    protected string $moduleNamespace = 'analytics';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(AnalyticsService::class);
    }
}
