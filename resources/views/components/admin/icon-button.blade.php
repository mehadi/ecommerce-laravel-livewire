{{-- ariaLabel is required; callers must always pass one so icon-only buttons stay accessible --}}
@props(['ariaLabel', 'title' => null])

<flux:button {{ $attributes->merge(['title' => $title ?? $ariaLabel, 'aria-label' => $ariaLabel]) }}>
    {{ $slot }}
</flux:button>
