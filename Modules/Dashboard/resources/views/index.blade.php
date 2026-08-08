<x-core::layouts.app>
<x-slot name="title">Dashboard</x-slot>

<x-slot name="header">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">
                {{ \Carbon\CarbonImmutable::now()->format('l, F j') }}
            </p>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $greeting }}, {{ auth()->user()->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Here's what's happening across your social accounts today.
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-3">
            <a href="{{ route('calendar.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-card ring-1 ring-gray-200 transition hover:bg-gray-50 hover:ring-gray-300 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800 dark:hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Calendar
            </a>
            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-brand-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:shadow-lg hover:shadow-brand-600/30 hover:brightness-110">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Post
            </a>
        </div>
    </div>
</x-slot>

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-core::stat-card label="Scheduled" value="{{ number_format($postCounts['scheduled'] ?? 0) }}" color="brand" :sub="$upcomingPosts->count().' coming up soon'">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </x-slot>
        </x-core::stat-card>

        <x-core::stat-card label="Published this week" value="{{ number_format($weekPosts) }}" color="green" :sub="\Carbon\CarbonImmutable::now()->startOfWeek()->format('M j').' — '.\Carbon\CarbonImmutable::now()->endOfWeek()->format('M j')">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
            </x-slot>
        </x-core::stat-card>

        <x-core::stat-card label="Total published" value="{{ number_format($publishedTotal) }}" color="purple" sub="All time">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
            </x-slot>
        </x-core::stat-card>

        <x-core::stat-card label="Connected accounts" value="{{ number_format($connectedAccountsCount) }}" color="sky" :sub="'of '.count(\Modules\SocialAccounts\Models\SocialAccount::PLATFORMS).' platforms'">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            </x-slot>
        </x-core::stat-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-core::card class="overflow-hidden">
                <x-slot name="actions">
                    <a href="{{ route('calendar.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">View calendar</a>
                </x-slot>

                @if ($upcomingPosts->isEmpty())
                    <div class="flex flex-col items-center justify-center gap-3 py-14 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-950/60 dark:text-brand-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Nothing scheduled yet</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Plan your next post and keep the momentum going.</p>
                        </div>
                        <a href="{{ route('posts.create') }}" class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Create your first post
                        </a>
                    </div>
                @else
                    <ol class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($upcomingPosts as $post)
                            <li>
                                <a href="{{ route('posts.show', $post) }}" class="group flex items-center gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <div class="flex shrink-0 flex-col items-center rounded-lg bg-gray-50 px-2.5 py-1.5 text-center ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                                        <span class="text-[10px] font-semibold uppercase text-gray-400">{{ $post->scheduled_at->format('M') }}</span>
                                        <span class="text-lg font-bold leading-tight text-gray-900 dark:text-white">{{ $post->scheduled_at->format('j') }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900 group-hover:text-brand-600 dark:text-white dark:group-hover:text-brand-400">
                                            {{ $post->title ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 60) ?: 'Untitled post' }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $post->scheduled_at->format('g:i A') }}</p>
                                    </div>
                                    <div class="hidden items-center -space-x-1.5 sm:flex">
                                        @foreach ($post->accounts as $account)
                                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white ring-2 ring-white dark:bg-gray-900 dark:ring-gray-900" title="{{ \Illuminate\Support\Str::title($account->platform) }}">
                                                <x-core::platform-icon :platform="$account->platform" class="h-4 w-4" />
                                            </span>
                                        @endforeach
                                    </div>
                                    <x-core::badge color="{{ $post->status === \Modules\Posts\Models\Post::STATUS_QUEUED ? 'purple' : 'brand' }}">
                                        {{ ucfirst($post->status) }}
                                    </x-core::badge>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-core::card>

            <x-core::card title="Weekly publishing activity">
                @if ($weekDays->sum('total') === 0)
                    <div class="flex items-center justify-center gap-3 py-10 text-sm text-gray-500 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-300 dark:text-gray-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" /></svg>
                        No posts scheduled this week yet.
                    </div>
                @else
                    <div class="flex h-44 items-end justify-between gap-2 sm:gap-4">
                        @foreach ($weekDays as $day)
                            <div class="group flex flex-1 flex-col items-center gap-2">
                                <div class="relative flex w-full flex-1 items-end">
                                    <div class="absolute -top-8 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-xs font-medium text-white opacity-0 shadow-lg transition group-hover:opacity-100 dark:bg-white dark:text-gray-900">
                                        {{ $day['total'] }} post{{ $day['total'] === 1 ? '' : 's' }}
                                    </div>
                                    <div
                                        class="w-full rounded-t-lg transition-all duration-300 {{ $day['total'] > 0 ? 'bg-gradient-to-t from-brand-600 to-purple-500 shadow-md shadow-brand-600/20 group-hover:brightness-110' : 'bg-gray-100 dark:bg-gray-800' }}"
                                        style="height: {{ $day['total'] > 0 ? max(8, round(($day['total'] / $maxActivity) * 100)) : 6 }}%"
                                    ></div>
                                </div>
                                <span class="text-[11px] font-medium {{ $day['date'] === now()->format('Y-m-d') ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-core::card>
        </div>

        <div class="space-y-6">
            <x-core::card>
                <x-slot name="actions">
                    <a href="{{ route('social-accounts.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">Manage</a>
                </x-slot>

                @if ($connectedAccounts->isEmpty())
                    <div class="flex flex-col items-center gap-3 py-8 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 dark:bg-sky-950/60 dark:text-sky-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-white">No accounts connected</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Connect your social profiles to start scheduling.</p>
                        <a href="{{ route('social-accounts.index') }}" class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-500">
                            Connect account
                        </a>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach ($connectedAccounts as $account)
                            <li class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                                    <x-core::platform-icon :platform="$account->platform" class="h-5 w-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $account->account_name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ \Illuminate\Support\Str::title($account->platform) }}</p>
                                </div>
                                <span class="flex h-2 w-2 items-center justify-center">
                                    <span class="absolute h-2 w-2 animate-ping rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative h-2 w-2 rounded-full bg-green-500"></span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-core::card>

            <x-core::card title="Posts by platform">
                @if ($platformDistribution->isEmpty())
                    <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No published posts yet.</p>
                @else
                    <ul class="space-y-4">
                        @php
                            $grandTotal = $platformDistribution->sum('total');
                            $bars = [
                                'facebook' => 'bg-[#1877F2]',
                                'instagram' => 'bg-gradient-to-r from-[#F58529] via-[#DD2A7B] to-[#8134AF]',
                                'linkedin' => 'bg-[#0A66C2]',
                                'twitter' => 'bg-gray-900 dark:bg-white',
                                'pinterest' => 'bg-[#E60023]',
                            ];
                        @endphp
                        @foreach ($platformDistribution as $row)
                            @php
                                $pct = $grandTotal > 0 ? round(($row['total'] / $grandTotal) * 100) : 0;
                            @endphp
                            <li>
                                <div class="mb-1.5 flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                        <x-core::platform-icon :platform="$row['platform']" class="h-4 w-4" />
                                        {{ \Illuminate\Support\Str::title($row['platform']) }}
                                    </span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $row['total'] }} · {{ $pct }}%</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $bars[$row['platform']] ?? 'bg-brand-500' }}" style="width: {{ max(2, $pct) }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-core::card>
        </div>
    </div>

    <x-core::card>
        <x-slot name="actions">
            <a href="{{ route('audit.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">View all</a>
        </x-slot>

        @if ($recentActivity->isEmpty())
            <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No activity recorded yet.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($recentActivity as $log)
                    <li class="flex items-center gap-3 py-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600 dark:bg-brand-950/60 dark:text-brand-400">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($log->user?->name ?? 'S', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm text-gray-900 dark:text-white">
                                <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $log->event }} {{ $log->entity_type }}</span>
                            </p>
                        </div>
                        <time class="shrink-0 text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at->diffForHumans() }}</time>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-core::card>
</div>
</x-core::layouts.app>
