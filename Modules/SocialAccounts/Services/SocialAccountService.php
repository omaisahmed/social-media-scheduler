<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Services;

use Modules\SocialAccounts\Models\SocialAccount;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;

final class SocialAccountService
{
    public function __construct(protected SocialAccountRepositoryInterface $accounts)
    {
    }

    public function connect(int $businessId, string $platform, array $credentials): SocialAccount
    {
        $account = $this->accounts->firstWhere('account_identifier', $credentials['account_identifier'] ?? '');

        $attributes = [
            'business_id' => $businessId,
            'platform' => $platform,
            'account_name' => $credentials['account_name'] ?? ucfirst($platform),
            'account_identifier' => $credentials['account_identifier'] ?? null,
            'avatar_url' => $credentials['avatar_url'] ?? null,
            'profile_url' => $credentials['profile_url'] ?? null,
            'access_token' => $credentials['access_token'] ?? null,
            'refresh_token' => $credentials['refresh_token'] ?? null,
            'token_expires_at' => $credentials['token_expires_at'] ?? null,
            'scopes' => $credentials['scopes'] ?? [],
            'metadata' => $credentials['metadata'] ?? [],
            'is_connected' => true,
            'connected_at' => now(),
            'last_synced_at' => now(),
        ];

        if ($account && $account->business_id === $businessId && $account->platform === $platform) {
            return $this->accounts->update($account->getKey(), $attributes);
        }

        return $this->accounts->create($attributes);
    }

    public function disconnect(int $businessId, int $accountId): bool
    {
        $account = $this->accounts->findForBusiness($businessId, $accountId);

        if (! $account) {
            return false;
        }

        return (bool) $this->accounts->update($accountId, ['is_connected' => false]);
    }

    public function markSynced(int $accountId): SocialAccount
    {
        return $this->accounts->update($accountId, ['last_synced_at' => now()]);
    }
}
