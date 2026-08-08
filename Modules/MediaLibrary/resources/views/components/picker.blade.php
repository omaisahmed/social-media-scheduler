@props([
    'name' => 'featured_media_id',
    'value' => null,
    'media' => collect(),
])

@php
    $images = $media->filter(fn ($asset) => $asset instanceof \Modules\MediaLibrary\Models\MediaAsset && $asset->isImage())
        ->values()
        ->map(fn ($asset) => [
            'id' => (int) $asset->getKey(),
            'url' => $asset->url(),
            'thumb' => $asset->thumbUrl(),
            'original_name' => $asset->original_name,
        ])
        ->values();
@endphp

<div x-data="mediaPicker({{ (int) $value }}, {{ \Illuminate\Support\Js::from($images) }})">
    <input type="hidden" name="{{ $name }}" :value="selectedId || ''" />

    <div class="flex flex-wrap items-center gap-4">
        <div class="relative h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-50 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
            <template x-if="previewUrl">
                <img :src="previewUrl" :alt="selectedName" class="h-full w-full object-cover">
            </template>
            <template x-if="!previewUrl">
                <div class="flex h-full w-full items-center justify-center text-gray-300 dark:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
            </template>
        </div>

        <div class="flex flex-col gap-2">
            <button type="button" @click="open = true"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span x-text="selectedId ? 'Change image' : 'Upload image'">Upload image</span>
            </button>
            <button type="button" x-show="selectedId" x-cloak @click="clear()"
                    class="text-left text-xs font-semibold text-red-600 transition hover:text-red-500 dark:text-red-400">
                Remove image
            </button>
            <p x-show="selectedName" x-cloak class="max-w-60 truncate text-xs text-gray-400 dark:text-gray-500" x-text="selectedName"></p>
        </div>
    </div>

    <x-input-error class="mt-2" :messages="$errors->get($name)" />

    <div x-cloak x-show="open" @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Media library</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Select an existing image or upload a new one.</p>
                </div>
                <button type="button" @click="open = false"
                        class="rounded-md p-1 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto px-5 py-4">
                <label @drop.prevent="drop($event)" @dragover.prevent
                       class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 px-4 py-8 text-center transition hover:border-brand-400 hover:bg-brand-50/40 dark:border-gray-700 dark:hover:border-brand-500 dark:hover:bg-brand-950/30">
                    <input type="file" x-ref="fileInput" multiple accept="image/*" class="hidden" @change="handleFileInput($event)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300" x-text="uploading ? 'Uploading...' : 'Drag & drop images here or click to browse'">
                        Drag & drop images here or click to browse
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">JPG, PNG, GIF, WebP — max 50MB</p>
                </label>

                <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-2 text-xs font-medium text-red-600 dark:text-red-400"></p>

                <div class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-5">
                    <template x-for="asset in assets" :key="asset.id">
                        <button type="button" @click="select(asset)"
                                class="group relative aspect-square overflow-hidden rounded-lg border-2 transition"
                                :class="selectedId === asset.id ? 'border-brand-500 ring-2 ring-brand-500/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'">
                            <img :src="asset.thumb" :alt="asset.original_name" loading="lazy"
                                 class="h-full w-full object-cover transition group-hover:opacity-90">
                            <span x-show="selectedId === asset.id" x-cloak
                                  class="absolute inset-0 flex items-center justify-center bg-brand-600/30">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6 text-white drop-shadow">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        </button>
                    </template>

                    <div x-show="assets.length === 0" x-cloak class="col-span-full rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No images yet. Upload one above to get started.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-5 py-3.5 dark:border-gray-800">
                <button type="button" @click="open = false"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button" @click="confirmSelection()" :disabled="!selectedId"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-brand-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow shadow-brand-600/25 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50">
                    Use this image
                </button>
            </div>
        </div>
    </div>

    <script>
        function mediaPicker(initialId, assets) {
            const selected = assets.find((a) => a.id === initialId) || null;

            return {
                assets: assets,
                selectedId: initialId,
                previewUrl: selected?.url || null,
                selectedName: selected?.original_name || '',
                open: false,
                uploading: false,
                uploadError: '',

                select(asset) {
                    this.selectedId = asset.id;
                    this.previewUrl = asset.url;
                    this.selectedName = asset.original_name;
                },

                confirmSelection() {
                    if (!this.selectedId) return;
                    this.open = false;
                },

                clear() {
                    this.selectedId = null;
                    this.previewUrl = null;
                    this.selectedName = '';
                },

                async upload(files) {
                    const fd = new FormData();
                    for (const file of files) fd.append('files[]', file);

                    this.uploading = true;
                    this.uploadError = '';

                    try {
                        const res = await fetch('{{ route('media.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        const data = await res.json().catch(() => ({}));

                        if (!res.ok) {
                            throw new Error(data.message || 'Upload failed. Please try again.');
                        }

                        (data.assets || []).forEach((asset) => {
                            if (!this.assets.some((a) => a.id === asset.id)) {
                                this.assets.unshift({
                                    id: asset.id,
                                    url: asset.url,
                                    thumb: asset.thumb_url || asset.url,
                                    original_name: asset.original_name,
                                });
                            }
                        });

                        if (data.assets?.length) {
                            this.select(data.assets[0]);
                        }
                    } catch (e) {
                        this.uploadError = e.message;
                    } finally {
                        this.uploading = false;
                        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                    }
                },

                handleFileInput(event) {
                    if (event.target.files?.length) this.upload(event.target.files);
                },

                drop(event) {
                    const files = event.dataTransfer?.files;
                    if (files?.length) this.upload(files);
                },
            };
        }
    </script>
</div>
