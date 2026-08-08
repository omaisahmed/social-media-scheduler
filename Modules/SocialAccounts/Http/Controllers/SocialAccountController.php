<?php

declare(strict_types=1);

namespace Modules\SocialAccounts\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAccounts\Models\SocialAccount;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;
use Modules\SocialAccounts\Services\SocialAccountService;

final class SocialAccountController
{
    public function __construct(
        protected SocialAccountService $accounts,
        protected SocialAccountRepositoryInterface $repository,
    ) {
    }

    public function index(Request $request): View
    {
        $businessId = $request->user()->business_id;

        return view('social-accounts::index', [
            'accounts' => $this->repository->connectedForBusiness($businessId),
            'platforms' => SocialAccount::PLATFORMS,
        ]);
    }

    public function connect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'in:'.implode(',', SocialAccount::PLATFORMS)],
            'account_name' => ['required', 'string', 'max:255'],
            'account_identifier' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
            'profile_url' => ['nullable', 'url', 'max:500'],
            'access_token' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->accounts->connect($request->user()->business_id, $validated['platform'], $validated);

        return redirect()->route('social-accounts.index')->with('status', 'social-account-connected');
    }

    public function disconnect(Request $request, int $accountId): RedirectResponse
    {
        $this->accounts->disconnect($request->user()->business_id, $accountId);

        return redirect()->route('social-accounts.index')->with('status', 'social-account-disconnected');
    }
}
