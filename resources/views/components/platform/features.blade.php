@php
    $features = [
        [
            'title' => __('Drag-and-drop landing pages'),
            'content' => __('Compose hero banners, featured products, testimonials, FAQs, and more into landing pages — no code required.'),
            'icon' => 'M4 6h16M4 12h10M4 18h16M17 15l3 3-3 3',
        ],
        [
            'title' => __('Full product catalog'),
            'content' => __('Products, categories, attributes, and variations (size, color, weight) with stock tracking built in.'),
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'title' => __('Flexible navigation builder'),
            'content' => __('Arrange menus, categories, and navbar components exactly how you want your storefront organized.'),
            'icon' => 'M4 6h16M4 12h16M4 18h7',
        ],
        [
            'title' => __('Custom domains & branding'),
            'content' => __('Connect your own domain and set your brand colors, logo, and typography from Website Settings.'),
            'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5',
        ],
        [
            'title' => __('Roles & permissions'),
            'content' => __('Invite your team and control exactly what each admin user can see and do.'),
            'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 2a4 4 0 10-8 0',
        ],
        [
            'title' => __('Coupons & shipping rules'),
            'content' => __('Run discount campaigns and configure per-city shipping rates that fit how you actually deliver.'),
            'icon' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.169.659 1.591l9.581 9.581c.699.699 1.83.699 2.528 0l7.16-7.16c.699-.698.699-1.83 0-2.528l-9.581-9.581A2.25 2.25 0 009.568 3z',
        ],
        [
            'title' => __('Cart, checkout & orders'),
            'content' => __('A ready-to-go shopping cart and secure checkout, with a full order-management dashboard.'),
            'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        [
            'title' => __('SEO & analytics built in'),
            'content' => __('Meta tags, sitemaps, and search-console verification alongside a built-in sales & traffic dashboard.'),
            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
    ];
@endphp

<section id="features" class="py-4 sm:py-5">
    <div class="container mx-auto px-4 sm:px-6 frontend-container">
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-widest ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 mb-4">
                {{ __('Features') }}
            </span>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white tracking-tight leading-tight text-balance">
                {{ __('Everything you need to run your store') }}
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 items-stretch">
            @foreach($features as $index => $feature)
                <div
                    x-data="{ shown: false }"
                    x-intersect.once="shown = true"
                    style="transition-delay: {{ min($index, 5) * 75 }}ms"
                    class="group h-full bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:ring-emerald-600/20 dark:hover:ring-emerald-500/30 transition-all duration-300 motion-reduce:transition-none hover:shadow-[0_8px_24px_-8px_rgb(16_24_40_/_0.10)] hover:-translate-y-1 motion-reduce:transform-none"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                >
                    <div class="space-y-5">
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 rounded-full flex items-center justify-center group-hover:scale-105 transition-transform duration-300 motion-reduce:transform-none">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-display font-semibold text-zinc-900 dark:text-white mb-2 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-200">
                                {{ $feature['title'] }}
                            </h3>
                            <p class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                {{ $feature['content'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</section>
