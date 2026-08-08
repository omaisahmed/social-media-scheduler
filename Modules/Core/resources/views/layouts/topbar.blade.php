<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-gray-200/80 bg-white/80 px-4 backdrop-blur-xl dark:border-gray-800 dark:bg-gray-950/80 sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-3">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div class="min-w-0">
            @if (isset($title))
                <h1 class="truncate text-sm font-semibold text-gray-900 dark:text-white lg:text-base">{{ $title }}</h1>
            @endif
            <p class="hidden text-xs text-gray-400 dark:text-gray-500 lg:block">
                @php
                    $now = \Carbon\CarbonImmutable::now();
                    $greeting = $now->hour < 12 ? 'Good morning' : ($now->hour < 18 ? 'Good afternoon' : 'Good evening');
                @endphp
                {{ $greeting }}, {{ auth()->user()->name }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <div class="relative hidden md:block">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input
                type="search"
                placeholder="Search posts, accounts..."
                class="w-56 rounded-lg border border-gray-200 bg-gray-50 py-1.5 pl-9 pr-8 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-800 dark:bg-gray-900 dark:text-white dark:focus:bg-gray-900 lg:w-64"
            >
            <kbd class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 rounded border border-gray-200 bg-white px-1 text-[10px] font-medium text-gray-400 dark:border-gray-700 dark:bg-gray-800">/</kbd>
        </div>

        <a href="{{ route('posts.create') }}" class="hidden items-center gap-1.5 rounded-lg bg-gradient-to-r from-brand-600 to-purple-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:shadow-lg hover:shadow-brand-600/30 hover:brightness-110 sm:inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Post
        </a>

        <x-core::notifications-dropdown />

        <div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative">
            <button @click="open = !open" class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-lg ring-2 ring-transparent transition hover:ring-brand-500/40 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <img src="{{ auth()->user()->avatar() }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 @click.away="open = false"
                 class="absolute right-0 z-50 mt-2 w-52 origin-top-right overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Profile
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                        Settings
                    </a>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
