<?php

declare(strict_types=1);

namespace Modules\Audit\Providers;

use Modules\Audit\Actions\RecordAuditAction;
use Modules\Core\Providers\ModuleServiceProvider;

final class AuditServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'audit';

    protected string $moduleNamespace = 'audit';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(RecordAuditAction::class);
    }
}
