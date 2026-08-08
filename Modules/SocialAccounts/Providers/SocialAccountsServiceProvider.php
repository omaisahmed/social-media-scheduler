<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;
use Modules\SocialAccounts\Repositories\SocialAccountRepository;

final class SocialAccountsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'social_accounts';

    protected string $moduleNamespace = 'social-accounts';

    public function register(): void
    {
        parent::register();

        $this->app->bind(SocialAccountRepositoryInterface::class, SocialAccountRepository::class);
    }
}
