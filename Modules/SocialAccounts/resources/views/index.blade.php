<x-core::layouts.app>
<x-slot name="title">Social accounts</x-slot>

<x-slot name="header">
    <x-core::page-header title="Social accounts" description="Connect the social networks you publish to." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-4">
        @forelse ($accounts as $account)
            <x-core::card>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($account->avatar_url)
                            <img src="{{ $account->avatar_url }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-gray-100 dark:ring-gray-700">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                                <x-core::platform-icon :platform="$account->platform" class="h-6 w-6" />
                            </span>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $account->account_name }}</h3>
                                <x-core::badge :platform="$account->platform">{{ $account->platformLabel() }}</x-core::badge>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $account->account_identifier }}
                                @if ($account->isExpired())
                                    · <span class="font-medium text-red-600 dark:text-red-400">token expired</span>
                                @elseif ($account->tokenExpiringSoon())
                                    · <span class="font-medium text-amber-600 dark:text-amber-400">token expiring soon</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="hidden text-xs text-gray-500 dark:text-gray-400 sm:block">
                            Last sync: {{ $account->last_synced_at?->diffForHumans() ?? 'never' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600 dark:text-green-400">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                            </span>
                            Connected
                        </span>
                        <button type="button"
                                @click="$dispatch('confirm', {
                                    action: '{{ route('social-accounts.disconnect', $account) }}',
                                    method: 'DELETE',
                                    message: 'Disconnect {{ $account->account_name }} from this workspace?'
                                })"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Disconnect
                        </button>
                    </div>
                </div>
            </x-core::card>
        @empty
            <x-core::card>
                <div class="py-12 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 text-gray-400 dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                    </span>
                    <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">No connected accounts</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Connect your first social account to start publishing.</p>
                </div>
            </x-core::card>
        @endforelse
    </div>

    <div>
        <x-core::card title="Connect account" description="Add a new social profile.">
            <form method="POST" action="{{ route('social-accounts.connect') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="platform" value="Platform" />
                    <x-select id="platform" name="platform" class="mt-1.5" required>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform }}" @selected(old('platform') === $platform)>{{ ucfirst($platform) }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error class="mt-2" :messages="$errors->get('platform')" />
                </div>

                <div>
                    <x-input-label for="account_name" value="Account name" required />
                    <x-text-input id="account_name" name="account_name" class="mt-1.5"
                                  :value="old('account_name')" placeholder="e.g. My Brand" required />
                    <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
                </div>

                <div>
                    <x-input-label for="account_identifier" value="Account identifier (optional)" />
                    <x-text-input id="account_identifier" name="account_identifier" class="mt-1.5"
                                  :value="old('account_identifier')" />
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">For Facebook, use your numeric Page ID.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('account_identifier')" />
                </div>

                <div>
                    <x-input-label for="access_token" value="Access token (optional)" />
                    <textarea id="access_token" name="access_token" rows="2"
                              class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('access_token') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                        For Facebook: developers.facebook.com/tools/explorer → pick your app → get a Page access token
                        with <code>pages_manage_posts</code> permission and paste it here.
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('access_token')" />
                </div>

                <div>
                    <x-input-label for="profile_url" value="Profile URL (optional)" />
                    <div class="relative mt-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                        <x-text-input id="profile_url" name="profile_url" type="url" class="pl-9"
                                      :value="old('profile_url')" placeholder="https://facebook.com/yourpage" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('profile_url')" />
                </div>

                <x-primary-button class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    Connect account
                </x-primary-button>
            </form>
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
