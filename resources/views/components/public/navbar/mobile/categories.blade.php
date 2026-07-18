@if($categories->count() > 0)
    <div class="bg-white dark:bg-zinc-900 rounded-[1.75rem] p-2 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] space-y-0.5">
        <div class="px-4 pt-2 pb-1 text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ __('Categories') }}</div>
        @foreach($categories as $category)
            @php
                $categoryUrl = '/category/'.$category->slug;
                $isActive = request()->is('category/'.$category->slug) || request()->is('category/'.$category->slug.'/*');
            @endphp
            <a href="{{ $categoryUrl }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="block px-4 py-3 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $isActive ? 'bg-zinc-100 dark:bg-white/[0.08] text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
@endif
