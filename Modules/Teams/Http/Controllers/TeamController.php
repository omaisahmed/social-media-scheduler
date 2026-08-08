<?php

declare(strict_types=1);

namespace Modules\Teams\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Teams\Repositories\Contracts\TeamInvitationRepositoryInterface;
use Modules\Teams\Repositories\Contracts\TeamMemberRepositoryInterface;
use Modules\Teams\Services\TeamService;

final class TeamController
{
    public function __construct(
        protected TeamService $teams,
        protected TeamMemberRepositoryInterface $members,
        protected TeamInvitationRepositoryInterface $invitations,
    ) {
    }

    public function index(Request $request): View
    {
        $businessId = $request->user()->business_id;

        return view('teams::index', [
            'members' => $this->members->forBusiness($businessId, ['user']),
            'invitations' => $this->invitations->pendingForBusiness($businessId, ['inviter']),
            'canManage' => $request->user()->canManageTeam(),
            'roles' => \Modules\Teams\Models\TeamMember::ROLES,
        ]);
    }

    public function updateRole(Request $request, int $userId): RedirectResponse
    {
        abort_unless($request->user()->canManageTeam(), 403);

        $this->teams->setRole($request->user()->business_id, $userId, $request->input('role'));

        return back()->with('status', 'team-role-updated');
    }

    public function remove(Request $request, int $userId): RedirectResponse
    {
        abort_unless($request->user()->canManageTeam(), 403);

        try {
            $this->teams->removeMember($request->user()->business_id, $userId, $request->user()->getKey());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['member' => $e->getMessage()]);
        }

        return back()->with('status', 'team-member-removed');
    }
}
