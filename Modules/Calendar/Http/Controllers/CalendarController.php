<?php

declare(strict_types=1);

namespace Modules\Calendar\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Support\Timezone;
use Modules\Posts\Repositories\Contracts\PostRepositoryInterface;

final class CalendarController
{
    public function __construct(protected PostRepositoryInterface $posts)
    {
    }

    public function index(Request $request): View
    {
        $timezone = Timezone::for((int) ($request->user()->business_id ?? 0));
        $month = CarbonImmutable::parse($request->query('month', now($timezone)->format('Y-m')), $timezone)->startOfMonth();

        $start = $month->startOfWeek();
        $end = $month->endOfMonth()->endOfWeek();

        $posts = $this->posts->forDateRange($request->user()->business_id, $start, $end);

        $grouped = $posts->groupBy(fn ($post) => $post->scheduled_at->format('Y-m-d'));

        return view('calendar::index', [
            'month' => $month,
            'weeks' => $this->weeks($start, $end),
            'posts' => $grouped,
        ]);
    }

    /**
     * @return array<int, array<int, CarbonImmutable>>
     */
    protected function weeks(CarbonImmutable $start, CarbonImmutable $end): array
    {
        $weeks = [];

        for ($cursor = $start; $cursor->lte($end); $cursor = $cursor->addWeek()) {
            $days = [];

            for ($i = 0; $i < 7; $i++) {
                $days[] = $cursor->addDays($i);
            }

            $weeks[] = $days;
        }

        return $weeks;
    }
}
