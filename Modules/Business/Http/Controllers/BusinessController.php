<?php

declare(strict_types=1);

namespace Modules\Business\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Business\Http\Requests\StoreBusinessRequest;
use Modules\Business\Http\Requests\UpdateBusinessRequest;
use Modules\Business\Models\Business;
use Modules\Business\Repositories\Contracts\BusinessRepositoryInterface;
use Modules\Business\Services\BusinessService;

final class BusinessController
{
    public function __construct(
        protected BusinessService $businesses,
        protected BusinessRepositoryInterface $repository,
    ) {
    }

    public function index(): View
    {
        return view('business::index', [
            'businesses' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('business::create');
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $this->businesses->create($request->validated(), $request->user()->getKey());

        return redirect()->route('business.index')->with('status', 'business-created');
    }

    public function edit(Business $business): View
    {
        abort_if($business->getKey() !== auth()->user()->business_id, 403);

        return view('business::edit', ['business' => $business]);
    }

    public function update(UpdateBusinessRequest $request, Business $business): RedirectResponse
    {
        abort_if($business->getKey() !== auth()->user()->business_id, 403);

        $this->businesses->update($business->getKey(), $request->validated());

        return redirect()->route('business.edit', $business)->with('status', 'business-updated');
    }

    public function destroy(Business $business): RedirectResponse
    {
        abort_if($business->getKey() !== auth()->user()->business_id, 403);

        $this->businesses->delete($business->getKey());

        return redirect()->route('dashboard.index')->with('status', 'business-deleted');
    }
}
