<?php

declare(strict_types=1);

namespace Modules\MediaLibrary\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\MediaLibrary\Services\MediaLibraryService;

final class MediaLibraryServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'media-library';

    protected string $moduleNamespace = 'media-library';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(MediaLibraryService::class);
    }
}
