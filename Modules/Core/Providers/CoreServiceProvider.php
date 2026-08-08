<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Blade;
use Modules\Core\Repositories\Contracts\RepositoryInterface;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Support\ModuleManager;

/**
 * Registers the Core module and discovers every other module in the
 * application. This provider is the single entry point that makes the
 * modular monolith boot in the correct order.
 */
final class CoreServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'core';

    protected string $moduleNamespace = 'core';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(ModuleManager::class, fn ($app) => new ModuleManager($app));

        $this->app->bind(RepositoryInterface::class, BaseRepository::class);

        $manager = $this->app->make(ModuleManager::class);

        // Core is bootstrapped explicitly through bootstrap/providers.php,
        // so tell discovery to skip it.
        $manager->markAsRegistered(static::class);

        $manager->registerModules();
    }

    public function boot(): void
    {
        parent::boot();

        Blade::componentNamespace('Modules\\Core\\View\\Components', 'core');
    }
}
