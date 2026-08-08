<div x-data="{ open: false }" @keydown.escape.window="open = false" class="relative">
    <button @click="open = !open" class="relative rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</p>
            @if ($unreadCount > 0)
                <button type="button" x-on:click="open = false" onclick="fetch('{{ route('notifications.read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })" class="text-xs text-brand-600 hover:text-brand-500 dark:text-brand-400">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($notifications as $notification)
                <a href="{{ route('notifications.index') }}" class="flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-indigo-500' }}"></span>
                    <span>
                        <span class="block text-sm {{ $notification->read_at ? 'text-gray-600 dark:text-gray-400' : 'font-medium text-gray-900 dark:text-white' }}">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $notification->data['message'] ?? '' }}
                        </span>
                        <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </span>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    No notifications yet
                </div>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" class="block border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center text-xs font-medium text-indigo-600 hover:bg-gray-50 dark:text-indigo-400 dark:hover:bg-gray-700/50">
            View all notifications
        </a>
    </div>
</div>
