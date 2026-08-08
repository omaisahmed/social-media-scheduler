@props([
    'name' => 'content',
    'value' => '',
    'placeholder' => 'Write your post content here...',
])

<div
    {{ $attributes->merge(['class' => 'rich-editor overflow-hidden rounded-xl border border-gray-200 bg-gray-50 shadow-sm transition focus-within:border-brand-400 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900 dark:focus-within:border-brand-500 dark:focus-within:bg-gray-900']) }}
    x-data="richEditor({{ \Illuminate\Support\Js::from(['name' => $name, 'value' => $value, 'placeholder' => $placeholder]) }})"
>
    <input type="hidden" :name="name" x-ref="input" :value="value" />

    <div x-ref="toolbar" aria-label="Formatting toolbar">
        <span class="ql-formats">
            <select class="ql-header">
                <option value="1"></option>
                <option value="2"></option>
                <option value="3"></option>
                <option selected></option>
            </select>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-bold"></button>
            <button type="button" class="ql-italic"></button>
            <button type="button" class="ql-underline"></button>
            <button type="button" class="ql-strike"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-blockquote"></button>
            <button type="button" class="ql-code-block"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-list" value="ordered"></button>
            <button type="button" class="ql-list" value="bullet"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-link"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-clean"></button>
        </span>
    </div>

    <div x-ref="editor"></div>
</div>
