<?php

declare(strict_types=1);

namespace Modules\Analytics\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Analytics\Services\AnalyticsService;

final class AnalyticsController
{
    public function __construct(protected AnalyticsService $analytics)
    {
    }

    public function index(Request $request): View
    {
        $start = CarbonImmutable::parse($request->query('from', now()->subDays(30)->format('Y-m-d')))->startOfDay();
        $end = CarbonImmutable::parse($request->query('to', now()->format('Y-m-d')))->endOfDay();

        return view('analytics::index', [
            'summary' => $this->analytics->aggregate($request->user()->business_id, $start, $end),
            'series' => $this->analytics->dailySeries($request->user()->business_id, $start, $end),
            'platforms' => $this->analytics->byPlatform($request->user()->business_id, $start, $end),
            'from' => $start->format('Y-m-d'),
            'to' => $end->format('Y-m-d'),
        ]);
    }
}
