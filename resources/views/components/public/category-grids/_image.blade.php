{{-- Category image or a gradient box-icon placeholder. Expects $category;
     optional $imgClass (applied to the <img>), $iconClass (placeholder icon
     size/color) and $placeholderClass (placeholder wrapper background). --}}
@if($category->image)
    <img
        src="{{ asset('storage/'.$category->image) }}"
        alt="{{ $category->name }}"
        loading="lazy"
        class="{{ $imgClass ?? 'w-full h-full object-cover' }}"
    >
@else
    <div class="w-full h-full flex items-center justify-center {{ $placeholderClass ?? 'bg-gradient-to-br from-emerald-50 to-zinc-50 dark:from-emerald-900/10 dark:to-zinc-900' }}">
        <svg class="{{ $iconClass ?? 'w-10 h-10 text-emerald-300/70 dark:text-emerald-700/50' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
    </div>
@endif
