@props(['categories'])

@if($categories && $categories->count() > 0)
    <section id="featured-categories" class="py-4 sm:py-5" aria-label="{{ __('Shop by category') }}">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
                <x-public.section-header
                    :eyebrow="__('Categories')"
                    :title="__('Shop by Category')"
                    :viewAllUrl="route('categories.index')"
                    :viewAllContext="__('Categories')"
                />

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach($categories as $category)
                        <a
                            wire:key="featured-category-{{ $category->id }}"
                            href="{{ route('category.show', $category->slug) }}"
                            wire:navigate
                            class="group flex flex-col items-center text-center gap-3"
                        >
                            <div class="relative w-full aspect-square overflow-hidden rounded-3xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] group-hover:ring-emerald-600/20 dark:group-hover:ring-emerald-500/30 group-hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] group-hover:-translate-y-1 transition-all duration-300 motion-reduce:transition-none motion-reduce:transform-none">
                                @if($category->image)
                                    <img
                                        src="{{ asset('storage/'.$category->image) }}"
                                        alt="{{ $category->name }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 motion-reduce:transform-none"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm sm:text-[15px] font-semibold text-zinc-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200">
                                    {{ $category->name }}
                                </p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 tabular-nums">
                                    {{ trans_choice(':count Product|:count Products', $category->products_count, ['count' => $category->products_count]) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <a
                    href="{{ route('categories.index') }}"
                    wire:navigate
                    class="sm:hidden mt-8 inline-flex items-center justify-center gap-1.5 w-full px-5 py-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] text-sm font-semibold text-emerald-700 dark:text-emerald-400"
                >
                    {{ __('View All Categories') }}
                </a>
            </div>
        </div>
    </section>
@endif
