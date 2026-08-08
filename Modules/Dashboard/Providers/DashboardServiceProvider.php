<?php

declare(strict_types=1);

namespace Modules\Dashboard\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class DashboardServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'dashboard';

    protected string $moduleNamespace = 'dashboard';
}
