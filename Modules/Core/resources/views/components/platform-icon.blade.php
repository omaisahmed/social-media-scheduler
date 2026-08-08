@props(['platform' => 'generic', 'size' => 'h-5 w-5'])

@php
    $component = new \Modules\Core\View\Components\PlatformIcon($platform, $size);
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" {{ $attributes->merge(['class' => $size.' '.$component->colorClass()]) }}>
    <path d="{{ $component->icon() }}" />
</svg>
