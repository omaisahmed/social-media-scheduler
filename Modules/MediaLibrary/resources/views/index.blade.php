<x-core::layouts.app>
<x-slot name="title">Media library</x-slot>

<x-slot name="header">
    <x-core::page-header title="Media library" description="Store and manage your images, videos and files." />
</x-slot>

<div class="space-y-6" x-data="mediaLibrary()">
    <x-core::card>
        <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data"
              x-data="{ uploading: false, dragging: false, handleDrop(e) { this.dragging = false; const input = this.$refs.fileInput; if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change', { bubbles: true })); } } }"
              @submit="uploading = true">
            @csrf
            <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 px-6 py-8 text-center transition hover:border-brand-400 hover:bg-brand-50/30 dark:border-gray-700 dark:bg-gray-900/50 dark:hover:border-brand-500 dark:hover:bg-brand-950/20"
                 :class="{ 'border-brand-500 bg-brand-50/40 dark:bg-brand-950/30': dragging }"
                 @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="handleDrop($event)">
                <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-500 shadow-card ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                </span>
                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-show="!uploading">Drop files here or click to browse</p>
                <p class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-show="uploading">Uploading...</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Up to 10 files, 50 MB each.</p>
                <input x-ref="fileInput" type="file" name="files[]" multiple required
                       accept="image/*,video/*,audio/*,.pdf,.docx,.txt,.csv"
                       @change="$el.form.submit()"
                       class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Files upload automatically. JPG, PNG, GIF, WebP, SVG, MP4, MOV, MP3, WAV, PDF, DOCX, TXT, CSV.</p>
            <x-input-error :messages="$errors->all()" />
        </form>
    </x-core::card>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('media.index') }}" class="flex items-center gap-2">
            <x-text-input name="search" value="{{ $search ?? '' }}" placeholder="Search files..." class="w-56" />
            <x-secondary-button type="submit">Search</x-secondary-button>
        </form>
        <div class="flex items-center gap-2">
            <a href="{{ route('media.index') }}" class="text-sm font-medium {{ empty($type) ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 hover:text-gray-700' }}">All</a>
            @foreach (['image', 'video', 'audio', 'document'] as $filterType)
                <a href="{{ route('media.index', ['type' => $filterType]) }}"
                   class="text-sm font-medium {{ $type === $filterType ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ ucfirst($filterType) }}s
                </a>
            @endforeach
        </div>
    </div>

    @if ($assets->isEmpty())
        <x-core::card>
            <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">No media yet. Upload your first file above.</p>
        </x-core::card>
    @else
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <x-secondary-button type="button" @click="toggleAll()"
                                    x-text="selected.length === assetIds.length ? 'Deselect all' : 'Select all'">Select all</x-secondary-button>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-show="selected.length > 0" x-cloak>
                    <span x-text="selected.length"></span> selected
                </p>
            </div>
            <button type="button" x-show="selected.length > 0" x-cloak
                    @click="$dispatch('confirm', {
                        action: '{{ route('media.bulk-destroy') }}',
                        method: 'POST',
                        message: 'Delete ' + selected.length + ' selected file(s) from storage?',
                        data: selected.map(id => ({ name: 'assets[]', value: id }))
                    })"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-red-600 to-rose-600 px-3.5 py-2 text-sm font-semibold text-white shadow shadow-red-600/30 transition hover:from-red-500 hover:to-rose-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                Delete selected (<span x-text="selected.length"></span>)
            </button>
        </div>

        <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($assets as $asset)
                <x-core::card :padding="false">
                    <div class="p-3">
                    <div class="relative aspect-square overflow-hidden rounded-lg bg-gray-100 transition dark:bg-gray-800"
                         :class="selected.includes({{ $asset->getKey() }}) ? 'ring-2 ring-brand-500 ring-offset-2 ring-offset-white dark:ring-offset-gray-900' : ''">
                        <label class="absolute left-2 top-2 z-10 flex h-7 w-7 cursor-pointer items-center justify-center rounded-lg bg-white/95 shadow ring-1 ring-gray-200 transition hover:ring-brand-400 dark:bg-gray-900/95 dark:ring-gray-700"
                               :class="selected.includes({{ $asset->getKey() }}) ? 'ring-2 ring-brand-500' : ''">
                            <input type="checkbox" class="h-4 w-4 cursor-pointer rounded border-gray-300 text-brand-600 focus:ring-brand-500/40 dark:border-gray-600 dark:bg-gray-800 dark:text-brand-500"
                                   :checked="selected.includes({{ $asset->getKey() }})"
                                   @change="toggle({{ $asset->getKey() }})">
                        </label>
                        @if ($asset->isImage())
                            <img src="{{ $asset->thumbUrl() }}" alt="{{ $asset->original_name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-4xl">
                                {{ match ($asset->type) {
                                    'video' => '🎬',
                                    'audio' => '🎵',
                                    default => '📄',
                                } }}
                            </div>
                        @endif
                        <button type="button" title="Delete"
                                @click="$dispatch('confirm', {
                                    action: '{{ route('media.destroy', $asset) }}',
                                    method: 'DELETE',
                                    message: 'Delete this file from storage?'
                                })"
                                class="absolute right-2 top-2 flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1.5 text-xs font-semibold text-red-600 shadow ring-1 ring-red-100 transition hover:bg-red-600 hover:text-white hover:ring-red-600 dark:bg-gray-900/95 dark:ring-red-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            Delete
                        </button>
                    </div>
                    <p class="mt-2 truncate text-sm text-gray-900 dark:text-white" title="{{ $asset->original_name }}">{{ $asset->original_name }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($asset->size / 1024, 1) }} KB</p>
                    </div>
                </x-core::card>
            @endforeach
        </div>

        <div>{{ $assets->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
    function mediaLibrary() {
        return {
            assetIds: {{ $assets->pluck('id')->toJson() }},
            selected: [],
            toggle(id) {
                const i = this.selected.indexOf(id);
                if (i === -1) {
                    this.selected.push(id);
                } else {
                    this.selected.splice(i, 1);
                }
            },
            toggleAll() {
                this.selected = this.selected.length === this.assetIds.length ? [] : [...this.assetIds];
            },
        };
    }
</script>
@endpush
</x-core::layouts.app>
