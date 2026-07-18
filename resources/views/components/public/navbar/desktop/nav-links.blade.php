<div class="hidden lg:flex items-center gap-0.5" x-data="{ openDropdown: null }">
    @foreach($navigationItems as $navItem)
        @php
            $hasChildren = $navItem->children && $navItem->children->where('is_active', true)->count() > 0;
            $children = $hasChildren ? $navItem->children->where('is_active', true)->sortBy('order') : collect();

            $isActive = false;
            if ($navItem->type === 'route' && $navItem->route_name) {
                $isActive = request()->routeIs($navItem->route_name);
            } elseif ($navItem->type === 'section') {
                $isActive = request()->url() === url($navItem->url);
            } else {
                $isActive = request()->url() === url($navItem->url) || request()->is(parse_url($navItem->url, PHP_URL_PATH));
            }

            // Check if any child is active
            $childActive = false;
            foreach ($children as $child) {
                if ($child->type === 'route' && $child->route_name) {
                    $childActive = request()->routeIs($child->route_name) || $childActive;
                } elseif ($child->type === 'section') {
                    $childActive = request()->url() === url($child->url) || $childActive;
                } else {
                    $childActive = request()->url() === url($child->url) || request()->is(parse_url($child->url, PHP_URL_PATH)) || $childActive;
                }
            }
            $isActive = $isActive || $childActive;

            $linkClasses = $isActive
                ? 'text-zinc-900 dark:text-white bg-zinc-100 dark:bg-white/[0.08]'
                : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100/70 dark:hover:bg-white/[0.06]';
        @endphp

        @if($hasChildren)
            <div class="relative"
                @mouseenter="openDropdown = {{ $navItem->id }}"
                @mouseleave="openDropdown = null"
            >
                <a href="{{ $navItem->resolved_url }}"
                    @if($navItem->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                    @focus="openDropdown = {{ $navItem->id }}"
                    aria-haspopup="true"
                    :aria-expanded="openDropdown === {{ $navItem->id }} ? 'true' : 'false'"
                    class="block px-3.5 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $linkClasses }}">
                    <span class="flex items-center gap-1.5">
                        @if($navItem->icon)
                            <flux:icon name="{{ $navItem->icon }}" class="w-4 h-4" />
                        @endif
                        {{ $navItem->label }}
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openDropdown === {{ $navItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </a>

                {{-- Dropdown --}}
                <div
                    x-show="openDropdown === {{ $navItem->id }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute top-full left-0 mt-3 w-52 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] overflow-hidden p-1.5 z-50"
                    style="display: none;"
                >
                    @foreach($children as $child)
                        @php
                            $childIsActive = false;
                            if ($child->type === 'route' && $child->route_name) {
                                $childIsActive = request()->routeIs($child->route_name);
                            } elseif ($child->type === 'section') {
                                $childIsActive = request()->url() === url($child->url);
                            } else {
                                $childIsActive = request()->url() === url($child->url) || request()->is(parse_url($child->url, PHP_URL_PATH));
                            }
                        @endphp
                        <a href="{{ $child->resolved_url }}"
                            @if($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                            class="block px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $childIsActive ? 'bg-zinc-100 dark:bg-white/[0.08] text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white' }}">
                            <span class="flex items-center gap-2">
                                @if($child->icon)
                                    <flux:icon name="{{ $child->icon }}" class="w-4 h-4" />
                                @endif
                                {{ $child->label }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $navItem->resolved_url }}"
                @if($navItem->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                class="block px-3.5 py-2 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $linkClasses }}">
                <span class="flex items-center gap-1.5">
                    @if($navItem->icon)
                        <flux:icon name="{{ $navItem->icon }}" class="w-4 h-4" />
                    @endif
                    {{ $navItem->label }}
                </span>
            </a>
        @endif
    @endforeach
</div>
