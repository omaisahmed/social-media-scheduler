<?php

declare(strict_types=1);

namespace Modules\Scheduler\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Scheduler\Models\BestTimeWindow;
use Modules\Scheduler\Services\BestTimeService;
use Modules\SocialAccounts\Models\SocialAccount;

final class SchedulerController
{
    public function __construct(protected BestTimeService $bestTime) {}

    public function index(Request $request): View
    {
        $businessId = $request->user()->business_id;

        return view('scheduler::index', [
            'windows' => BestTimeWindow::withoutBusinessScope(fn () => BestTimeWindow::query()
                ->where('business_id', $businessId)
                ->orderBy('platform')
                ->orderBy('day_of_week')
                ->get()),
            'platforms' => SocialAccount::PLATFORMS,
            'days' => BestTimeWindow::DAYS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform' => ['required', 'in:'.implode(',', SocialAccount::PLATFORMS)],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        BestTimeWindow::withoutBusinessScope(fn () => BestTimeWindow::query()->updateOrCreate(
            [
                'business_id' => $request->user()->business_id,
                'platform' => $validated['platform'],
                'day_of_week' => $validated['day_of_week'],
            ],
            $validated,
        ));

        return back()->with('status', 'best-time-saved');
    }

    public function destroy(Request $request, int $windowId): RedirectResponse
    {
        BestTimeWindow::withoutBusinessScope(fn () => BestTimeWindow::query()
            ->where('business_id', $request->user()->business_id)
            ->whereKey($windowId)
            ->delete());

        return back()->with('status', 'best-time-deleted');
    }
}
