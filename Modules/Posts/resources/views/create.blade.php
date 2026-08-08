<x-core::layouts.app>
<x-slot name="title">New post</x-slot>

<x-slot name="header">
    <x-core::page-header title="New post" description="Compose a post and choose where to publish it." />
</x-slot>

<form method="POST" action="{{ route('posts.store') }}" class="space-y-6">
    @csrf

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-core::card title="Compose your post" description="Write something worth sharing.">
                <div class="space-y-5">
                    <div>
                        <x-input-label for="title" value="Title (internal)" />
                        <x-text-input id="title" name="title" class="mt-1.5"
                                      :value="old('title')" placeholder="e.g. Product launch announcement" />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="content" value="Post content" />
                            <span class="text-xs text-gray-400 dark:text-gray-500">Rich text supported</span>
                        </div>
                        <x-posts::editor name="content" :value="old('content')" placeholder="Write your post content here..." class="mt-1.5" />
                        <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    </div>

                    <div>
                        <x-input-label for="hashtags" value="Hashtags (optional)" />
                        <x-text-input id="hashtags" name="hashtags" class="mt-1.5"
                                      :value="old('hashtags')" placeholder="#DigitalTransformation #ICTLeadership #Maqaam" />
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Space-separated. They are appended to the post when published.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('hashtags')" />
                    </div>
                </div>
            </x-core::card>

            <x-core::card title="Featured image" description="Pick an image to accompany this post.">
                <x-media-library::picker name="featured_media_id" :value="old('featured_media_id')" :media="$media" />
            </x-core::card>

            <x-core::card title="Schedule" description="When should this post go live?">
                <div>
                    <x-input-label for="scheduled_at" value="Schedule for (optional)" />
                    <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local" class="mt-1.5"
                                  :value="old('scheduled_at')" />
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Leave empty to save as a draft.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('scheduled_at')" />
                </div>
            </x-core::card>
        </div>

        <div>
            <x-core::card title="Publish to" description="Select the accounts that receive this post."
                          class="lg:sticky lg:top-24">
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    @forelse ($accounts as $account)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-3 transition duration-150 hover:border-gray-300 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50/60 has-[:checked]:ring-2 has-[:checked]:ring-brand-500/20 dark:border-gray-800 dark:hover:border-gray-700 dark:has-[:checked]:border-brand-500 dark:has-[:checked]:bg-brand-950/40">
                            <x-checkbox name="account_ids[]" :value="$account->getKey()"
                                        :checked="old('account_ids') && in_array($account->getKey(), old('account_ids'))" />
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                                <x-core::platform-icon :platform="$account->platform" class="h-4.5 w-4.5" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-gray-900 dark:text-white">{{ $account->account_name }}</span>
                                <span class="block text-xs text-gray-400 dark:text-gray-500">{{ ucfirst($account->platform) }}</span>
                            </span>
                        </label>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No connected accounts.
                                <a href="{{ route('social-accounts.index') }}" class="font-medium text-brand-600 hover:text-brand-500">Connect one first</a>.
                            </p>
                        </div>
                    @endforelse
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('account_ids')" />

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('posts.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Cancel</a>
                        <x-primary-button>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Save post
                        </x-primary-button>
                        <button type="submit" name="publish" value="1"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Publish now
                        </button>
                    </div>
                </x-slot>
            </x-core::card>
        </div>
    </div>
</form>
</x-core::layouts.app>
