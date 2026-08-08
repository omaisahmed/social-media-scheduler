<x-core::layouts.app>
<x-slot name="title">{{ $post->title ?? 'Post' }}</x-slot>

<x-slot name="header">
    <x-core::page-header :title="$post->title ?? 'Untitled post'" description="Post details and delivery status.">
        <div class="flex items-center gap-3">
            @if ($post->isScheduled())
                <form method="POST" action="{{ route('posts.cancel', $post) }}">
                    @csrf
                    <button class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </form>
            @endif
            @if (! $post->isPublished() && $post->status !== 'cancelled')
                <form method="POST" action="{{ route('posts.publish', $post) }}">
                    @csrf
                    <button class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-green-500 text-white hover:opacity-90">
                        Publish now
                    </button>
                </form>
            @endif
            @if (! $post->isPublished())
                <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
                    Edit
                </a>
            @endif
            <button type="button"
                    @click="$dispatch('confirm', {
                        action: '{{ route('posts.destroy', $post) }}',
                        method: 'DELETE',
                        message: 'Delete this post? It will also be removed from the connected Facebook page(s).'
                    })"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Delete
            </button>
        </div>
    </x-core::page-header>
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <x-core::card>
            <div class="flex items-center gap-2 mb-4">
                <x-core::badge :type="match($post->status) {
                    'published' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'gray',
                    'scheduled' => 'info',
                    default => 'default',
                }">{{ $post->statusLabel() }}</x-core::badge>

                @if ($post->scheduled_at)
                    <span class="text-sm text-gray-500 dark:text-gray-400">Scheduled {{ $post->scheduled_at->format('M j, Y g:i A') }}</span>
                @endif
            </div>

            @if ($post->featuredMedia)
                <img src="{{ $post->featuredMedia->url() }}" alt="{{ $post->featuredMedia->original_name }}"
                     class="mb-6 max-h-[28rem] w-full rounded-xl object-cover">
            @endif

            <div class="prose prose-gray dark:prose-invert max-w-none">
                {!! $post->renderableContent() !!}
            </div>

            @if ($post->hashtags)
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($post->hashtagList() as $tag)
                        <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 ring-1 ring-inset ring-brand-600/10 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            @if ($post->published_at)
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Published {{ $post->published_at->diffForHumans() }}
                    @if ($post->user)
                        by {{ $post->user->name }}
                    @endif
                </p>
            @endif
        </x-core::card>
    </div>

    <div>
        <x-core::card>
            <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Delivery</h2>
            <div class="space-y-3">
                @forelse ($post->accounts as $delivery)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-core::badge>{{ $delivery->platformLabel() }}</x-core::badge>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $delivery->accountName() }}</span>
                        </div>
                        <x-core::badge :type="match($delivery->status) {
                            'published' => 'success',
                            'failed' => 'danger',
                            default => 'default',
                        }">{{ ucfirst($delivery->status) }}</x-core::badge>
                    </div>
                    @if ($delivery->error)
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $delivery->error }}</p>
                    @endif
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No delivery targets.</p>
                @endforelse
            </div>
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
