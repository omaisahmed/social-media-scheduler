<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Audit\Models\AuditLog;
use Modules\Posts\Models\Post;
use Modules\Posts\Models\PostAccount;
use Modules\SocialAccounts\Models\SocialAccount;

final class DashboardController
{
    public function index(Request $request): View
    {
        $businessId = $request->user()->business_id;

        $weekStart = CarbonImmutable::now()->startOfWeek();
        $weekEnd = CarbonImmutable::now()->endOfWeek();

        $postsQuery = Post::withoutBusinessScope(fn () => Post::query()->where('business_id', $businessId));

        $weekActivity = (clone $postsQuery)
            ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->selectRaw('DATE(scheduled_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $platforms = DB::table('post_accounts')
            ->join('posts', 'posts.id', '=', 'post_accounts.post_id')
            ->where('posts.business_id', $businessId)
            ->selectRaw('post_accounts.platform, COUNT(*) as total')
            ->groupBy('post_accounts.platform')
            ->pluck('total', 'platform');

        $platformDistribution = collect(SocialAccount::PLATFORMS)
            ->map(fn ($platform) => [
                'platform' => $platform,
                'total' => (int) ($platforms[$platform] ?? 0),
            ])
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();

        $maxActivity = $weekActivity->max() ?: 1;

        return view('dashboard::index', [
            'greeting' => $this->greeting(),
            'business' => $request->user()->business,
            'postCounts' => (clone $postsQuery)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'weekPosts' => (clone $postsQuery)
                ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
                ->count(),
            'publishedTotal' => (clone $postsQuery)
                ->where('status', Post::STATUS_PUBLISHED)
                ->count(),
            'upcomingPosts' => (clone $postsQuery)
                ->whereIn('status', [Post::STATUS_SCHEDULED, Post::STATUS_QUEUED])
                ->with(['accounts', 'user'])
                ->orderBy('scheduled_at')
                ->limit(6)
                ->get(),
            'weekDays' => collect(range(0, 6))->map(function ($day) use ($weekStart, $weekActivity) {
                $date = $weekStart->addDays($day);

                return [
                    'label' => $date->format('D'),
                    'date' => $date->format('Y-m-d'),
                    'total' => (int) ($weekActivity[$date->format('Y-m-d')] ?? 0),
                ];
            }),
            'maxActivity' => $maxActivity,
            'platformDistribution' => $platformDistribution,
            'connectedAccounts' => SocialAccount::withoutBusinessScope(fn () => SocialAccount::query()
                ->where('business_id', $businessId)
                ->where('is_connected', true)
                ->orderBy('platform')
                ->get()),
            'connectedAccountsCount' => SocialAccount::withoutBusinessScope(fn () => SocialAccount::query()
                ->where('business_id', $businessId)
                ->where('is_connected', true)
                ->count()),
            'recentActivity' => AuditLog::withoutBusinessScope(fn () => AuditLog::query()
                ->where('business_id', $businessId)
                ->with('user')
                ->latest()
                ->limit(8)
                ->get()),
        ]);
    }

    private function greeting(): string
    {
        $hour = CarbonImmutable::now()->hour;

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };
    }
}
