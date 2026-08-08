<x-core::layouts.app>
<x-slot name="title">AI Composer</x-slot>

<x-slot name="header">
    <x-core::page-header title="AI Composer" description="Generate or improve post content with AI." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <x-core::card title="AI Composer" description="Describe what you want and let AI do the writing.">
            <form id="ai-form" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="action" value="Action" />
                        <x-select id="action" name="action" class="mt-1.5" :options="[
                            'generate' => 'Generate new',
                            'rewrite' => 'Rewrite',
                            'expand' => 'Expand',
                            'shorten' => 'Shorten',
                            'hashtags' => 'Hashtags',
                        ]" />
                    </div>
                    <div>
                        <x-input-label for="platform" value="Platform" />
                        <x-select id="platform" name="platform" class="mt-1.5">
                            <option value="">General</option>
                            <option>Facebook</option>
                            <option>Instagram</option>
                            <option>LinkedIn</option>
                            <option>Twitter</option>
                            <option>Pinterest</option>
                            <option>TikTok</option>
                        </x-select>
                    </div>
                    <div>
                        <x-input-label for="tone" value="Tone" />
                        <x-select id="tone" name="tone" class="mt-1.5" :options="[
                            'professional' => 'Professional',
                            'casual' => 'Casual',
                            'friendly' => 'Friendly',
                            'enthusiastic' => 'Enthusiastic',
                            'witty' => 'Witty',
                            'informative' => 'Informative',
                        ]" />
                    </div>
                </div>

                <div id="prompt-field">
                    <x-input-label for="prompt" value="Topic or instruction" />
                    <x-textarea id="prompt" name="prompt" rows="4" class="mt-1.5"
                                placeholder="e.g. Write a launch post for our new analytics feature..." />
                </div>

                <div id="content-field" class="hidden">
                    <x-input-label for="content" value="Existing content" />
                    <x-textarea id="content" name="content" rows="5" class="mt-1.5"
                                placeholder="Paste the content you want to improve..." />
                </div>

                <x-primary-button id="ai-submit" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
                    </svg>
                    Generate with AI
                </x-primary-button>

                <p id="ai-error" class="hidden text-sm font-medium text-red-600 dark:text-red-400"></p>
            </form>
        </x-core::card>

        <div id="ai-result" class="hidden">
            <x-core::card title="Result" description="Copy the generated content into a new post.">
                <x-slot name="actions">
                    <button type="button" id="ai-copy"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 dark:bg-brand-950/60 dark:text-brand-300 dark:hover:bg-brand-950">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                        </svg>
                        Copy
                    </button>
                </x-slot>
                <textarea id="ai-output" rows="8" readonly
                          class="w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-3 text-sm leading-relaxed text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
            </x-core::card>
        </div>
    </div>

    <div>
        <x-core::card title="How it works" description="A few tips for best results."
                      class="lg:sticky lg:top-24">
            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                <li class="flex gap-2.5">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600 dark:bg-brand-950/60 dark:text-brand-300">1</span>
                    Choose an action — generate fresh content or rewrite what you have.
                </li>
                <li class="flex gap-2.5">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600 dark:bg-brand-950/60 dark:text-brand-300">2</span>
                    Pick the platform and tone so the result fits the audience.
                </li>
                <li class="flex gap-2.5">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-600 dark:bg-brand-950/60 dark:text-brand-300">3</span>
                    Copy the output into a new post and schedule it.
                </li>
            </ul>
        </x-core::card>
    </div>
</div>

@push('scripts')
<script>
    const action = document.getElementById('action');
    const promptField = document.getElementById('prompt-field');
    const contentField = document.getElementById('content-field');

    function toggleFields() {
        const needsContent = ['rewrite', 'expand', 'shorten', 'hashtags'].includes(action.value);
        contentField.classList.toggle('hidden', !needsContent);
        promptField.classList.toggle('hidden', needsContent);
    }

    action.addEventListener('change', toggleFields);
    toggleFields();

    document.getElementById('ai-submit').addEventListener('click', async () => {
        const error = document.getElementById('ai-error');
        const submit = document.getElementById('ai-submit');
        error.classList.add('hidden');

        const payload = {
            action: action.value,
            platform: document.getElementById('platform').value,
            tone: document.getElementById('tone').value,
            prompt: document.getElementById('prompt').value,
            content: document.getElementById('content').value,
        };

        submit.disabled = true;
        submit.textContent = 'Generating...';

        try {
            const res = await fetch('{{ route('ai.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Generation failed');
            }

            const output = document.getElementById('ai-output');
            output.value = data.content;
            document.getElementById('ai-result').classList.remove('hidden');
        } catch (e) {
            error.textContent = e.message;
            error.classList.remove('hidden');
        } finally {
            submit.disabled = false;
            submit.textContent = 'Generate with AI';
        }
    });

    document.getElementById('ai-copy').addEventListener('click', () => {
        const output = document.getElementById('ai-output');
        output.select();
        navigator.clipboard.writeText(output.value);
    });
</script>
@endpush
</x-core::layouts.app>
