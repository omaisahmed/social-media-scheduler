@php
    $navGroups = [
        [
            'label' => 'Overview',
            'items' => [
                ['name' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75'],
                ['name' => 'Calendar', 'route' => 'calendar.index', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                ['name' => 'Analytics', 'route' => 'analytics.index', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
            ],
        ],
        [
            'label' => 'Publishing',
            'items' => [
                ['name' => 'Posts', 'route' => 'posts.index', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
                ['name' => 'Scheduler', 'route' => 'scheduler.index', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['name' => 'Templates', 'route' => 'templates.index', 'icon' => 'M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42'],
            ],
        ],
        [
            'label' => 'Content',
            'items' => [
                ['name' => 'Media Library', 'route' => 'media.index', 'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z'],
                ['name' => 'Contacts', 'route' => 'contacts.index', 'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ['name' => 'AI Assistant', 'route' => 'ai.index', 'icon' => 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5'],
            ],
        ],
        [
            'label' => 'Insights',
            'items' => [
                ['name' => 'Reports', 'route' => 'reports.index', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                ['name' => 'Audit log', 'route' => 'audit.index', 'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9'],
            ],
        ],
        [
            'label' => 'Workspace',
            'items' => [
                ['name' => 'Social Accounts', 'route' => 'social-accounts.index', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                ['name' => 'Team', 'route' => 'teams.index', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                ['name' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
                ['name' => 'Settings', 'route' => 'settings.index', 'icon' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75'],
            ],
        ],
    ];

    $current = request()->route()?->getName();
    $activeCount = auth()->user()?->unreadNotifications?->count() ?? 0;
@endphp

<template x-teleport="body">
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" @click="closeSidebar()"></div>
</template>

<aside
    class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-mesh-dark transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    @keydown.escape.window="closeSidebar()"
>
    <div class="flex h-full flex-col">
        <div class="flex h-16 shrink-0 items-center justify-between px-5">
            <a href="{{ route('dashboard.index') }}" class="group flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-lg shadow-brand-500/30">
                    <x-core::application-logo class="h-5 w-5" />
                </span>
                <span class="text-[15px] font-bold tracking-tight text-white">{{ config('app.name') }}</span>
            </a>
            <button @click="closeSidebar()" class="lg:hidden rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 pb-4 pt-2">
            @foreach ($navGroups as $group)
                <div>
                    <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">{{ $group['label'] }}</p>
                    <ul class="space-y-1">
                        @foreach ($group['items'] as $item)
                            @php
                                $groupKey = explode('.', $item['route'])[0];
                                $active = str_starts_with((string) $current, $groupKey);
                            @endphp
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 {{ $active ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                >
                                    @if ($active)
                                        <span class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-gradient-to-b from-brand-400 to-purple-500"></span>
                                    @endif
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5 shrink-0 {{ $active ? 'text-brand-300' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                    </svg>
                                    <span class="flex-1 truncate">{{ $item['name'] }}</span>
                                    @if ($item['route'] === 'notifications.index' && $activeCount > 0)
                                        <span class="rounded-full bg-gradient-to-r from-brand-500 to-purple-500 px-1.5 py-0.5 text-[10px] font-semibold text-white">{{ $activeCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="shrink-0 space-y-3 p-4">
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-brand-600/20 via-brand-500/10 to-purple-600/20 p-4 ring-1 ring-white/10">
                <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-brand-500/20 blur-2xl"></div>
                <p class="text-sm font-semibold text-white">Scheduler Pro</p>
                <p class="mt-1 text-xs leading-relaxed text-slate-400">Unlock AI writing, priority publishing &amp; advanced analytics.</p>
                <a href="{{ route('settings.index') }}" class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-brand-500 to-purple-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg shadow-brand-500/25 transition hover:brightness-110">
                    Upgrade plan
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3 ring-1 ring-white/10">
                <img src="{{ auth()->user()->avatar() }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-lg object-cover ring-2 ring-white/10">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                </div>
                <button
                    @click="darkMode = !darkMode; setTheme(darkMode ? 'dark' : 'light')"
                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                    title="Toggle theme"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5" x-show="!darkMode">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5" x-cloak x-show="darkMode">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</aside>
