<?php

declare(strict_types=1);

namespace Modules\Core\Support;

use Illuminate\Contracts\Foundation\Application;
use Modules\Core\Providers\ModuleServiceProvider;

/**
 * Discovers every module registered inside the Modules directory and
 * registers its service provider(s) with the application container.
 *
 * Adding a new module is as simple as dropping a folder (with a
 * Providers/*ServiceProvider.php class) into Modules/. No further
 * registration is required.
 */
final class ModuleManager
{
    /**
     * List of discovered module providers.
     *
     * @var array<int, ModuleServiceProvider>
     */
    protected array $providers = [];

    /**
     * The root directory modules live in.
     */
    protected string $modulesPath;

    /**
     * Provider classes already registered by this manager.
     *
     * @var array<string, true>
     */
    protected array $registeredProviders = [];

    public function __construct(protected Application $app, ?string $modulesPath = null)
    {
        $this->modulesPath = $modulesPath ?? $app->basePath('Modules');
    }

    /**
     * Mark a provider class as already registered so discovery will skip it.
     */
    public function markAsRegistered(string $providerClass): void
    {
        $this->registeredProviders[$providerClass] = true;
    }

    /**
     * Register every module provider found on disk.
     */
    public function registerModules(): void
    {
        if (! is_dir($this->modulesPath)) {
            return;
        }

        foreach (glob($this->modulesPath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) ?: [] as $moduleDirectory) {
            $moduleName = basename($moduleDirectory);
            $this->registerProvidersFrom($moduleName, $moduleDirectory.DIRECTORY_SEPARATOR.'Providers');
        }
    }

    /**
     * @return array<int, ModuleServiceProvider>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    protected function registerProvidersFrom(string $moduleName, string $providersDirectory): void
    {
        if (! is_dir($providersDirectory)) {
            return;
        }

        foreach (glob($providersDirectory.DIRECTORY_SEPARATOR.'*ServiceProvider.php') ?: [] as $providerFile) {
            $providerClass = sprintf('Modules\\%s\\Providers\\%s', $moduleName, basename($providerFile, '.php'));

            if (! class_exists($providerClass)) {
                continue;
            }

            $reflection = new \ReflectionClass($providerClass);

            if (! $reflection->isSubclassOf(ModuleServiceProvider::class)) {
                continue;
            }

            // Avoid re-registering providers (e.g. Core itself) that are
            // already registered through bootstrap/providers.php.
            if (isset($this->registeredProviders[$providerClass])
                || ! empty($this->app->getProvider($providerClass))) {
                continue;
            }

            $this->registeredProviders[$providerClass] = true;

            /** @var ModuleServiceProvider $provider */
            $provider = new $providerClass($this->app);
            $this->app->register($provider);
            $this->providers[] = $provider;
        }
    }
}
