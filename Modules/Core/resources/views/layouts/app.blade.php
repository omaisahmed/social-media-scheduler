<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="appShell()" x-init="init" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' . config('app.name') : config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen lg:flex">
            @include('core::layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0 bg-mesh-light dark:bg-mesh-dark dark:bg-gray-950">
                @include('core::layouts.topbar')

                <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                    @if (session('status'))
                        <div class="mb-4">
                            <x-core::alert type="success" :message="session('status')" dismissible />
                        </div>
                    @endif

                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>

                @include('core::layouts.footer')
            </div>
        </div>

        @stack('modals')

        <div x-cloak x-show="confirm.show"
             x-on:confirm.window="openConfirm($event.detail)"
             x-on:keydown.escape.window="closeConfirm()"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="confirm.show" x-transition.opacity
                 class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeConfirm()"></div>
            <div x-show="confirm.show"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
                <div class="flex items-start gap-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </span>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Confirm action</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="confirm.message"></p>
                    </div>
                    <button type="button" @click="closeConfirm()"
                            class="rounded-md p-1 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="closeConfirm()"
                            class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" @click="confirmSubmit()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-red-600 to-rose-600 px-4 py-2 text-sm font-semibold text-white shadow shadow-red-600/30 transition hover:from-red-500 hover:to-rose-500">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        <script>
            function appShell() {
                return {
                    sidebarOpen: false,
                    darkMode: localStorage.getItem('theme') === 'dark' ||
                        (localStorage.getItem('theme') === 'system' &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches),

                    init() {
                        this.applyTheme();
                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                            if (localStorage.getItem('theme') === 'system') {
                                this.darkMode = e.matches;
                                this.applyTheme();
                            }
                        });
                    },

                    applyTheme() {
                        document.documentElement.classList.toggle('dark', this.darkMode);
                    },

                    setTheme(theme) {
                        localStorage.setItem('theme', theme);
                        this.darkMode = theme === 'dark' ||
                            (theme === 'system' &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);
                        this.applyTheme();
                    },

                    closeSidebar() {
                        this.sidebarOpen = false;
                    },

                    confirm: {
                        show: false,
                        action: null,
                        method: 'POST',
                        message: 'Are you sure?',
                        data: [],
                    },

                    openConfirm({ action, method = 'POST', message = 'Are you sure?', data = [] }) {
                        this.confirm.action = action;
                        this.confirm.method = method;
                        this.confirm.message = message;
                        this.confirm.data = data;
                        this.confirm.show = true;
                    },

                    closeConfirm() {
                        this.confirm.show = false;
                    },

                    confirmSubmit() {
                        const { action, method, data } = this.confirm;
                        if (! action) return;

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = action;
                        form.style.display = 'none';

                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                        form.appendChild(token);

                        if (method !== 'POST') {
                            const methodField = document.createElement('input');
                            methodField.type = 'hidden';
                            methodField.name = '_method';
                            methodField.value = method;
                            form.appendChild(methodField);
                        }

                        (data || []).forEach(({ name, value }) => {
                            const field = document.createElement('input');
                            field.type = 'hidden';
                            field.name = name;
                            field.value = value;
                            form.appendChild(field);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    },
                };
            }
        </script>

        @stack('scripts')
    </body>
</html>
