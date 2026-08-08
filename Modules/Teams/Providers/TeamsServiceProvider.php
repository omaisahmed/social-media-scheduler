<?php

declare(strict_types=1);

namespace Modules\Teams\Providers;

use Modules\Core\Providers\ModuleServiceProvider;
use Modules\Teams\Repositories\Contracts\TeamInvitationRepositoryInterface;
use Modules\Teams\Repositories\Contracts\TeamMemberRepositoryInterface;
use Modules\Teams\Repositories\TeamInvitationRepository;
use Modules\Teams\Repositories\TeamMemberRepository;

final class TeamsServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'teams';

    protected string $moduleNamespace = 'teams';

    public function register(): void
    {
        parent::register();

        $this->app->bind(TeamMemberRepositoryInterface::class, TeamMemberRepository::class);
        $this->app->bind(TeamInvitationRepositoryInterface::class, TeamInvitationRepository::class);
    }
}
