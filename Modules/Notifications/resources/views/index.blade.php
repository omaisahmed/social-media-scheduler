<x-core::layouts.app>
<x-slot name="title">Notifications</x-slot>

<x-slot name="header">
    <x-core::page-header title="Notifications" description="Activity across your workspace.">
        @if ($notifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-secondary-button type="submit">Mark all as read</x-secondary-button>
            </form>
        @endif
    </x-core::page-header>
</x-slot>

<x-core::card>
    @if ($notifications->isEmpty())
        <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">You're all caught up.</p>
    @else
        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Notification';
                    $body = $data['body'] ?? '';
                @endphp
                <div class="flex items-start justify-between gap-4 py-3">
                    <div class="flex-1">
                        <p class="text-sm font-medium {{ $notification->read_at ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $title }}
                        </p>
                        @if ($body)
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $body }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @unless ($notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <x-secondary-button type="submit" class="!px-3 !py-1">Mark read</x-secondary-button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</x-core::card>
</x-core::layouts.app>
