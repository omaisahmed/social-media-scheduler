<x-core::layouts.app>
<x-slot name="title">Templates</x-slot>

<x-slot name="header">
    <x-core::page-header title="Templates" description="Reusable content templates for your posts.">
        <a href="{{ route('templates.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-500">
            New template
        </a>
    </x-core::page-header>
</x-slot>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <form method="GET" action="{{ route('templates.index') }}" class="flex items-center gap-2">
            <x-text-input name="search" value="{{ request('search') }}" placeholder="Search templates..." class="w-64" />
            <x-secondary-button type="submit">Search</x-secondary-button>
        </form>
    </div>

    @if ($templates->isEmpty())
        <x-core::card>
            <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                No templates yet. <a href="{{ route('templates.create') }}" class="font-medium text-indigo-600 hover:underline">Create your first template</a>.
            </p>
        </x-core::card>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($templates as $template)
                <x-core::card class="flex flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            @if (! empty($template->tags))
                                @foreach ($template->tags as $tag)
                                    <x-core::badge>{{ $tag }}</x-core::badge>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <p class="mt-2 line-clamp-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ Str::limit($template->content ?? 'No content', 140) }}
                    </p>
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-800 pt-3">
                        <span class="text-xs text-gray-400">Updated {{ $template->updated_at->diffForHumans() }}</span>
                        <div class="flex items-center gap-3 text-sm">
                            <a href="{{ route('templates.edit', $template) }}" class="font-medium text-indigo-600 hover:underline">Edit</a>
                            <button type="button"
                                    @click="$dispatch('confirm', {
                                        action: '{{ route('templates.destroy', $template) }}',
                                        method: 'DELETE',
                                        message: 'Delete this template?'
                                    })"
                                    class="font-medium text-red-600 hover:underline">Delete</button>
                        </div>
                    </div>
                </x-core::card>
            @endforeach
        </div>

        <div>{{ $templates->links() }}</div>
    @endif
</div>
</x-core::layouts.app>
