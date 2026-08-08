<?php

declare(strict_types=1);

namespace Modules\Teams\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Teams\Services\TeamService;

final class InvitationController
{
    public function __construct(protected TeamService $teams)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageTeam(), 403);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,member,viewer'],
        ]);

        try {
            $this->teams->invite(
                $request->user()->business_id,
                $request->user()->getKey(),
                $validated['email'],
                $validated['role'],
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }

        return back()->with('status', 'team-invitation-sent');
    }

    public function revoke(Request $request, int $invitationId): RedirectResponse
    {
        abort_unless($request->user()->canManageTeam(), 403);

        $this->teams->revoke($invitationId);

        return back()->with('status', 'team-invitation-revoked');
    }
}
