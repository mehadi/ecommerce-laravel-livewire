@if($categories->count() > 0)
    <div class="hidden lg:flex items-center gap-0.5">
        @foreach($categories as $category)
            @php
                $categoryUrl = '/category/'.$category->slug;
                $isActive = request()->is('category/'.$category->slug) || request()->is('category/'.$category->slug.'/*');
            @endphp
            <a href="{{ $categoryUrl }}"
                wire:navigate
                class="block px-3.5 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $isActive ? 'text-zinc-900 dark:text-white bg-zinc-100 dark:bg-white/[0.08]' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-white/[0.06]' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
@endif
