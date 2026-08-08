<x-core::layouts.app>
<x-slot name="title">Audit log</x-slot>

<x-slot name="header">
    <x-core::page-header title="Audit log" description="Track changes across your workspace." />
</x-slot>

<x-core::card>
    <form method="GET" action="{{ route('audit.index') }}" class="mb-4 flex flex-wrap items-center gap-3">
        <select name="event" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
            <option value="">All events</option>
            @foreach ($events as $event)
                <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
            @endforeach
        </select>
        <x-secondary-button type="submit">Filter</x-secondary-button>
        @if (request('event'))
            <a href="{{ route('audit.index') }}" class="text-sm font-medium text-gray-500 hover:underline">Clear</a>
        @endif
    </form>

    @if ($logs->isEmpty())
        <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">No audit entries yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Event</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Target</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-4 py-2">
                                <x-core::badge>{{ $log->event }}</x-core::badge>
                            </td>
                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                {{ $log->user?->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            </td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</x-core::card>
</x-core::layouts.app>
