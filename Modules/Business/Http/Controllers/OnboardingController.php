<?php

declare(strict_types=1);

namespace Modules\Business\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Business\Http\Requests\StoreBusinessRequest;
use Modules\Business\Services\BusinessService;

final class OnboardingController
{
    public function __construct(protected BusinessService $businesses)
    {
    }

    public function show(): View
    {
        return view('business::onboarding.business');
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $business = $this->businesses->create($request->validated(), $request->user()->getKey());

        return redirect()->route('onboarding.next', $business)->with('status', 'business-created');
    }

    public function next(Request $request): View
    {
        return view('business::onboarding.invite-team', [
            'business' => $request->user()->business,
        ]);
    }
}
