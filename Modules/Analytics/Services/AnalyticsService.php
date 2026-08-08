<?php

declare(strict_types=1);

namespace Modules\Analytics\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\Analytics\Models\AnalyticsDaily;

final class AnalyticsService
{
    /**
     * Aggregate metrics over a date range for a business.
     */
    public function aggregate(int $businessId, CarbonImmutable $start, CarbonImmutable $end, ?int $accountId = null): array
    {
        $query = AnalyticsDaily::query()
            ->where('business_id', $businessId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $rows = $query->get();

        return [
            'impressions' => (int) $rows->sum('impressions'),
            'reach' => (int) $rows->sum('reach'),
            'engagements' => (int) $rows->sum('engagements'),
            'followers_delta' => (int) $rows->sum('followers_delta'),
            'engagement_rate' => (int) $rows->sum('reach') > 0
                ? round(((int) $rows->sum('engagements') / (int) $rows->sum('reach')) * 100, 2)
                : 0.0,
        ];
    }

    /**
     * @return Collection<int, array{date: string, impressions: int, reach: int, engagements: int}>
     */
    public function dailySeries(int $businessId, CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return AnalyticsDaily::query()
            ->where('business_id', $businessId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, SUM(impressions) as impressions, SUM(reach) as reach, SUM(engagements) as engagements')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn (AnalyticsDaily $row) => [
                'date' => $row->date->format('Y-m-d'),
                'impressions' => (int) $row->impressions,
                'reach' => (int) $row->reach,
                'engagements' => (int) $row->engagements,
            ]);
    }

    /**
     * @return Collection<int, array{platform: string, impressions: int, engagements: int}>
     */
    public function byPlatform(int $businessId, CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        return AnalyticsDaily::query()
            ->where('business_id', $businessId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('platform, SUM(impressions) as impressions, SUM(engagements) as engagements')
            ->groupBy('platform')
            ->orderByDesc('impressions')
            ->get()
            ->map(fn (AnalyticsDaily $row) => [
                'platform' => $row->platform,
                'impressions' => (int) $row->impressions,
                'engagements' => (int) $row->engagements,
            ]);
    }
}
