<x-core::layouts.app>
<x-slot name="title">Analytics</x-slot>

<x-slot name="header">
    <x-core::page-header title="Analytics" description="Track performance across your social accounts.">
        <form method="GET" action="{{ route('analytics.index') }}" class="flex items-center gap-2">
            <x-text-input name="from" type="date" value="{{ $from }}" class="w-40" />
            <x-text-input name="to" type="date" value="{{ $to }}" class="w-40" />
            <x-secondary-button type="submit">Apply</x-secondary-button>
        </form>
    </x-core::page-header>
</x-slot>

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-core::stat-card label="Impressions" value="{{ number_format($summary['impressions']) }}" />
        <x-core::stat-card label="Reach" value="{{ number_format($summary['reach']) }}" />
        <x-core::stat-card label="Engagements" value="{{ number_format($summary['engagements']) }}" />
        <x-core::stat-card label="Engagement rate" value="{{ $summary['engagement_rate'] }}%" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-core::card class="lg:col-span-2">
            <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Daily performance</h2>

            @if ($series->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    No analytics data for this range yet. Data appears once social accounts sync.
                </p>
            @else
                <div class="flex items-end gap-1 h-48">
                    @foreach ($series as $point)
                        @php
                            $max = max($series->max('impressions'), 1);
                            $height = round(($point['impressions'] / $max) * 100);
                        @endphp
                        <div class="group relative flex-1 flex items-end h-full">
                            <div class="w-full rounded-t bg-indigo-500/80 group-hover:bg-indigo-500 transition-all"
                                 style="height: {{ max($height, 2) }}%"
                                 title="{{ $point['date'] }}: {{ number_format($point['impressions']) }} impressions"></div>
                            <span class="absolute -top-6 hidden group-hover:block text-xs text-gray-600 dark:text-gray-300">
                                {{ \Carbon\CarbonImmutable::parse($point['date'])->format('M j') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-core::card>

        <x-core::card>
            <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">By platform</h2>

            @if ($platforms->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">No data yet.</p>
            @else
                <ul class="space-y-3">
                    @foreach ($platforms as $platform)
                        <li class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($platform['platform']) }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($platform['impressions']) }} impressions</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
