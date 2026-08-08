<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Base service provider every module should extend.
 *
 * It wires the module's migrations, routes, views and config into the
 * framework automatically based on the module's directory layout, which
 * keeps every module self-contained while remaining boot-time cheap.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The snake_case module identifier used as the view namespace,
     * config namespace and route prefix. e.g. "social_accounts".
     */
    protected string $moduleName;

    /**
     * The studly module identifier used for view namespaces that map
     * directly to the directory. e.g. "SocialAccounts".
     */
    protected string $moduleNamespace;

    public function register(): void
    {
        $this->mergeModuleConfig();
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleViews();
        $this->loadModuleTranslations();
    }

    protected function modulePath(): string
    {
        return module_path($this->moduleName);
    }

    protected function mergeModuleConfig(): void
    {
        $config = $this->modulePath().'/config.php';

        if (is_file($config)) {
            $this->mergeConfigFrom($config, $this->moduleName);
        }
    }

    protected function loadModuleMigrations(): void
    {
        $migrations = $this->modulePath().'/Database/Migrations';

        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }
    }

    protected function loadModuleRoutes(): void
    {
        $webRoutes = $this->modulePath().'/routes/web.php';
        $apiRoutes = $this->modulePath().'/routes/api.php';

        if (is_file($webRoutes)) {
            Route::middleware('web')
                ->group(function () use ($webRoutes) {
                    require $webRoutes;
                });
        }

        if (is_file($apiRoutes)) {
            Route::middleware('api')
                ->group(function () use ($apiRoutes) {
                    require $apiRoutes;
                });
        }
    }

    protected function loadModuleViews(): void
    {
        $views = $this->modulePath().'/resources/views';

        if (is_dir($views)) {
            $this->loadViewsFrom($views, $this->moduleNamespace);
        }
    }

    protected function loadModuleTranslations(): void
    {
        $translations = $this->modulePath().'/resources/lang';

        if (is_dir($translations)) {
            $this->loadTranslationsFrom($translations, $this->moduleNamespace);
        }
    }
}
