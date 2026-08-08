<x-core::layouts.app>
<x-slot name="title">Settings</x-slot>

<x-slot name="header">
    <x-core::page-header title="Settings" description="Manage your workspace and preferences." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <x-core::card title="Workspace settings" description="Basic details about your business.">
            <form method="POST" action="{{ route('settings.business') }}" class="space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <x-input-label for="name" value="Business name" required />
                        <x-text-input id="name" name="name" class="mt-1.5" :value="old('name', $business?->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="timezone" value="Timezone" />
                        <x-select id="timezone" name="timezone" class="mt-1.5" required>
                            @foreach (timezone_identifiers_list() as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', $business?->primary_timezone ?? config('app.timezone')) === $timezone)>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="default_platform" value="Default platform" />
                        <x-select id="default_platform" name="default_platform" class="mt-1.5" :options="['' => 'None']">
                            @foreach (\Modules\SocialAccounts\Models\SocialAccount::PLATFORMS as $platform)
                                <option value="{{ $platform }}" @selected(old('default_platform') === $platform)>{{ ucfirst($platform) }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
                    <x-primary-button>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                        </svg>
                        Save workspace
                    </x-primary-button>
                </div>
            </form>
        </x-core::card>
    </div>

    <div>
        <x-core::card title="Notification preferences" description="Choose what you want to hear about."
                      class="lg:sticky lg:top-24">
            <form method="POST" action="{{ route('settings.notifications') }}" class="space-y-5">
                @csrf

                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ([
                        'notify_post_published' => ['Post published', 'Get notified when one of your posts goes live.'],
                        'notify_post_failed' => ['Post failed to publish', 'Alerts when a scheduled post fails.'],
                        'notify_daily_summary' => ['Daily performance summary', 'A roundup of yesterday\u2019s performance.'],
                    ] as $key => [$label, $hint])
                        <div class="flex items-center justify-between gap-4 py-3.5">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</p>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <x-checkbox name="{{ $key }}" value="1" :checked="(bool) $preferences[$key]"
                                            class="peer sr-only" />
                                <span class="relative h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-gradient-to-r peer-checked:from-brand-500 peer-checked:to-purple-500 peer-focus:ring-2 peer-focus:ring-brand-500/40 dark:bg-gray-700">
                                    <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></span>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
                    <x-primary-button>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                        </svg>
                        Save preferences
                    </x-primary-button>
                </div>
            </form>
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
