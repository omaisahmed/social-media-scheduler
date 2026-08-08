<?php

declare(strict_types=1);

namespace Modules\Calendar\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class CalendarServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'calendar';

    protected string $moduleNamespace = 'calendar';
}
