<?php

declare(strict_types=1);

namespace Modules\Settings\Providers;

use Modules\Core\Providers\ModuleServiceProvider;

final class SettingsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'settings';

    protected string $moduleNamespace = 'settings';
}
