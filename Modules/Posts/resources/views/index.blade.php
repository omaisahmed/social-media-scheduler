<x-core::layouts.app>
<x-slot name="title">Posts</x-slot>

<x-slot name="header">
    <x-core::page-header title="Posts" description="Create, schedule and manage your social posts.">
        <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
            New post
        </a>
    </x-core::page-header>
</x-slot>

<div class="mb-6">
    <form method="GET" action="{{ route('posts.index') }}" class="flex flex-wrap items-center gap-3">
        <x-text-input name="search" type="text" placeholder="Search posts..." class="w-64"
                      :value="$filters['search'] ?? ''" />
        <select name="status" onchange="this.form.submit()"
                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @if ($filters['status'] ?? $filters['search'] ?? null)
            <a href="{{ route('posts.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400">Clear filters</a>
        @endif
    </form>
</div>

<div class="space-y-4">
    @forelse ($posts as $post)
        <x-core::card>
            <a href="{{ route('posts.show', $post) }}" class="block">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex items-start gap-4">
                        @if ($post->featuredMedia)
                            <img src="{{ $post->featuredMedia->url() }}" alt="" class="h-16 w-16 shrink-0 rounded-lg object-cover ring-1 ring-gray-100 dark:ring-gray-800">
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $post->title ?? 'Untitled post' }}
                                </h3>
                                <x-core::badge :type="match($post->status) {
                                    'published' => 'success',
                                    'failed' => 'danger',
                                    'cancelled' => 'gray',
                                    'scheduled' => 'info',
                                    default => 'default',
                                }">{{ $post->statusLabel() }}</x-core::badge>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ Str::limit(strip_tags(html_entity_decode($post->content ?? '')), 140) }}
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                        @if ($post->isScheduled())
                            <p>Planned for <span class="font-medium">{{ $post->scheduled_at->format('M j, Y g:i A') }}</span></p>
                        @elseif ($post->published_at)
                            <p>Published {{ $post->published_at->diffForHumans() }}</p>
                        @endif
                        <p class="mt-1 text-xs">{{ $post->accounts->count() }} platform(s)</p>
                    </div>
                </div>
            </a>
        </x-core::card>
    @empty
        <x-core::card>
            <div class="text-center py-12">
                <h3 class="font-semibold text-gray-900 dark:text-white">No posts found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Create your first post to get started.</p>
                <a href="{{ route('posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
                    Create post
                </a>
            </div>
        </x-core::card>
    @endforelse
</div>

@if ($posts->hasPages())
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endif
</x-core::layouts.app>
