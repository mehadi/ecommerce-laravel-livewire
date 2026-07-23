{{-- Subcategory chip list. Expects $subcategories; optional $chipClass and
     $moreClass override the pill styling, $take (default 4) caps how many
     chips render before the "+N more" summary, and $showMore (default true)
     toggles that summary off entirely. --}}
@php
    $take = $take ?? 4;
    $showMore = $showMore ?? true;
@endphp
@foreach($subcategories->take($take) as $subcategory)
    <a
        href="{{ route('category.show', $subcategory->slug) }}"
        wire:navigate
        class="{{ $chipClass ?? 'inline-flex items-center px-2.5 py-1 rounded-full bg-white dark:bg-zinc-900 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-emerald-600/30 dark:hover:ring-emerald-500/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500' }}"
    >
        {{ $subcategory->name }}
    </a>
@endforeach
@if($showMore && $subcategories->count() > $take)
    <span class="{{ $moreClass ?? 'text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 px-1' }}">
        {{ __('+:count more', ['count' => $subcategories->count() - $take]) }}
    </span>
@endif
