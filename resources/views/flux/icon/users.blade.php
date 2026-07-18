{{-- Credit: Custom icon inspired by Heroicons (https://heroicons.com) --}}

@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

<svg
    {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
    <path d="M17 21h5v-2a3 3 0 0 0-5.356-1.857" />
    <path d="M17 19H7" />
    <path d="M7 21H2v-2a3 3 0 0 1 5.356-1.857" />
    <path d="M17 21v-2c0-.656-.126-1.283-.356-1.857a5.002 5.002 0 0 0-9.288 0c-.23.574-.356 1.201-.356 1.857v2" />
    <path d="M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
    <path d="M19 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
    <path d="M7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
</svg>

