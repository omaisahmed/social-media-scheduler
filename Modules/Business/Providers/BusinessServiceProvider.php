<?php

declare(strict_types=1);

namespace Modules\Business\Providers;

use Modules\Business\Repositories\BusinessRepository;
use Modules\Business\Repositories\Contracts\BusinessRepositoryInterface;
use Modules\Core\Providers\ModuleServiceProvider;

final class BusinessServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'business';

    protected string $moduleNamespace = 'business';

    public function register(): void
    {
        parent::register();

        $this->app->bind(BusinessRepositoryInterface::class, BusinessRepository::class);
    }
}
