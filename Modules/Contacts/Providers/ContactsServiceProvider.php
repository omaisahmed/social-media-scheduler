<?php

declare(strict_types=1);

namespace Modules\Contacts\Providers;

use Modules\Contacts\Repositories\ContactRepository;
use Modules\Contacts\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Core\Providers\ModuleServiceProvider;

final class ContactsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'contacts';

    protected string $moduleNamespace = 'contacts';

    public function register(): void
    {
        parent::register();

        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
    }
}
