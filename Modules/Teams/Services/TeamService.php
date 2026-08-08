<?php

declare(strict_types=1);

namespace Modules\Teams\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Teams\Models\TeamInvitation;
use Modules\Teams\Models\TeamMember;
use Modules\Teams\Repositories\Contracts\TeamInvitationRepositoryInterface;
use Modules\Teams\Repositories\Contracts\TeamMemberRepositoryInterface;

final class TeamService
{
    public function __construct(
        protected TeamMemberRepositoryInterface $members,
        protected TeamInvitationRepositoryInterface $invitations,
    ) {
    }

    public function attachUser(int $businessId, int $userId, string $role = TeamMember::ROLE_MEMBER): TeamMember
    {
        $member = $this->members->findForBusiness($businessId, $userId);

        if ($member) {
            return $this->members->update($member->getKey(), ['role' => $role]);
        }

        return $this->members->create([
            'business_id' => $businessId,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function invite(int $businessId, int $invitedBy, string $email, string $role = TeamMember::ROLE_MEMBER): TeamInvitation
    {
        $existing = $this->invitations->firstWhere('email', $email);

        if ($existing && $existing->business_id === $businessId) {
            throw new \RuntimeException('An invitation for this email already exists.');
        }

        return $this->invitations->create([
            'business_id' => $businessId,
            'invited_by' => $invitedBy,
            'email' => Str::lower($email),
            'role' => $role,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function accept(string $token, int $userId): ?TeamMember
    {
        $invitation = $this->invitations->findByToken($token);

        if (! $invitation || ! $invitation->isPending() || $invitation->isExpired()) {
            return null;
        }

        return DB::transaction(function () use ($invitation, $userId) {
            $this->invitations->update($invitation->getKey(), ['accepted_at' => now()]);

            return $this->attachUser($invitation->business_id, $userId, $invitation->role);
        });
    }

    public function revoke(int $invitationId): bool
    {
        return (bool) $this->invitations->update($invitationId, ['revoked_at' => now()]);
    }

    public function removeMember(int $businessId, int $userId, int $actingUserId): bool
    {
        if ($userId === $actingUserId) {
            throw new \RuntimeException('You cannot remove yourself.');
        }

        return $this->members->deleteForBusiness($businessId, $userId);
    }

    public function setRole(int $businessId, int $userId, string $role): TeamMember
    {
        $memberId = $this->memberId($businessId, $userId);

        return $this->members->update($memberId, ['role' => $role]);
    }

    protected function memberId(int $businessId, int $userId): ?int
    {
        $member = $this->members->findForBusiness($businessId, $userId);

        return $member?->getKey();
    }
}
