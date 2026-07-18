@if($navigationItems->count() > 0)
    <div class="bg-white dark:bg-zinc-900 rounded-[1.75rem] p-2 ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] space-y-0.5"
        x-data="{ openMobileMenu: null }"
    >
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
            @endphp

            <div>
                <div class="flex items-center justify-between">
                    <a href="{{ $navItem->resolved_url }}"
                        @if($navItem->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                        @click="if (!{{ $hasChildren ? 'true' : 'false' }}) mobileMenuOpen = false"
                        class="flex-1 block px-4 py-3 rounded-full text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $isActive ? 'bg-zinc-100 dark:bg-white/[0.08] text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white' }}">
                        {{ $navItem->label }}
                    </a>

                    @if($hasChildren)
                        <button
                            @click="openMobileMenu = openMobileMenu === {{ $navItem->id }} ? null : {{ $navItem->id }}"
                            class="w-11 h-11 flex items-center justify-center rounded-full text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white"
                            :aria-expanded="openMobileMenu === {{ $navItem->id }} ? 'true' : 'false'"
                            aria-label="{{ __('Toggle submenu') }}"
                        >
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMobileMenu === {{ $navItem->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                @if($hasChildren)
                    <div
                        x-show="openMobileMenu === {{ $navItem->id }}"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-96"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 max-h-96"
                        x-transition:leave-end="opacity-0 max-h-0"
                        class="overflow-hidden"
                        style="display: none;"
                    >
                        <div class="ml-4 mt-1 mb-1 space-y-0.5 border-l-2 border-zinc-100 dark:border-zinc-800 pl-3">
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
                                    @click="mobileMenuOpen = false"
                                    class="block px-4 py-2.5 rounded-full text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-900 dark:focus-visible:ring-white {{ $childIsActive ? 'bg-zinc-100 dark:bg-white/[0.08] text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white' }}">
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
