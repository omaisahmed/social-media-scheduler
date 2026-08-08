<?php

declare(strict_types=1);

namespace Modules\Notifications\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class NotificationsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'notifications';

    protected string $moduleNamespace = 'notifications';
}
