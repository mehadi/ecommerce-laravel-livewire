<div class="w-full space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-1">
            <flux:heading size="lg">{{ __('Create Landing Page') }}</flux:heading>
            <flux:text size="sm" variant="subtle">
                {{ __('Design a custom landing page with product selection, section configuration, and SEO optimization.') }}
            </flux:text>
        </div>

        <flux:button :href="route('admin.landing-pages.index')" wire:navigate variant="ghost" size="sm">
            {{ __('Back to Landing Pages') }}
        </flux:button>
    </div>

    {{-- Duplicate From Existing --}}
    <section class="space-y-4 rounded-lg border border-amber-200 bg-amber-50/50 p-6 dark:border-amber-800 dark:bg-amber-900/20">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <flux:heading size="sm">{{ __('Quick Start: Duplicate from Existing') }}</flux:heading>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Save time by duplicating settings from an existing landing page') }}</p>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <flux:field>
                <flux:label>{{ __('Duplicate From') }}</flux:label>
                <flux:select wire:model.live="duplicateFrom" wire:change="duplicateFromExisting">
                    <option value="">{{ __('Start from scratch') }}</option>
                    @foreach($existingLandingPages as $existing)
                        <option value="{{ $existing->id }}">{{ $existing->name }}</option>
                    @endforeach
                </flux:select>
                <flux:description>{{ __('Select an existing landing page to copy its configuration') }}</flux:description>
            </flux:field>
        </div>
    </section>

    <form wire:submit="save" class="space-y-6">
        <section class="space-y-6 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <flux:heading size="sm">{{ __('Basic Information') }}</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Set the name, URL, and product for your landing page') }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Name') }} *</flux:label>
                    <flux:input wire:model.live="name" placeholder="{{ __('e.g. Summer Sale Landing Page') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('URL Slug') }} *</flux:label>
                    <flux:input wire:model.live="slug" placeholder="{{ __('e.g. summer-sale') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Auto-generated from name. If unavailable, a numbered suffix will be added (e.g. summer-sale-1)') }}</p>
                    <flux:error name="slug" />
                </flux:field>
            </div>

            <div class="grid gap-6">
                <flux:field>
                    <flux:label>{{ __('Product') }}</flux:label>
                    <flux:select wire:model="product_id">
                        <option value="">{{ __('Select a product (optional)') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name_en }}</option>
                        @endforeach
                    </flux:select>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('The selected product will be used in the hero section and featured product section') }}</p>
                    <flux:error name="product_id" />
                </flux:field>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Meta Title') }}</flux:label>
                    <flux:input wire:model.live="meta_title" placeholder="{{ __('SEO title for search engines') }}" maxlength="255" />
                    <flux:description>
                        <span class="{{ strlen($meta_title) > 60 ? 'text-red-600 dark:text-red-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                            {{ strlen($meta_title) }}/60 {{ __('characters recommended') }}
                        </span>
                    </flux:description>
                    <flux:error name="meta_title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Meta Description') }}</flux:label>
                    <flux:textarea wire:model.live="meta_description" rows="2" placeholder="{{ __('SEO description for search engines') }}" maxlength="500" />
                    <flux:description>
                        <span class="{{ strlen($meta_description) > 160 ? 'text-red-600 dark:text-red-400' : 'text-zinc-500 dark:text-zinc-400' }}">
                            {{ strlen($meta_description) }}/160 {{ __('characters recommended') }}
                        </span>
                    </flux:description>
                    <flux:error name="meta_description" />
                </flux:field>
            </div>

            {{-- SEO Preview --}}
            @if($meta_title || $meta_description)
                <div class="rounded-lg border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <p class="mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Search Engine Preview') }}</p>
                    <div class="space-y-2">
                        <div class="text-sm">
                            <p class="text-blue-600 dark:text-blue-400 line-clamp-1">
                                {{ $meta_title ?: $name ?: __('Your Landing Page Title') }}
                            </p>
                            <p class="text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                {{ url('/lp/' . ($slug ?: 'your-slug')) }}
                            </p>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300 line-clamp-2">
                                {{ $meta_description ?: __('This is how your landing page will appear in search engine results.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="space-y-6 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                    <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <flux:heading size="sm">{{ __('Hero Section') }}</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('The pinned first section — always visible, tied to the selected product') }}</p>
                </div>
            </div>

            <div class="grid gap-6">
                <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/40 flex-shrink-0 mt-0.5">
                            <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('Hero & Featured Product Section') }}</p>
                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">{{ __('The product selected above will automatically be displayed in the hero section and featured product section. Customize the hero text below.') }}</p>
                        </div>
                    </div>
                </div>

                <flux:field>
                    <flux:label>{{ __('Hero Badge Text') }}</flux:label>
                    <flux:input wire:model="heroBadgeText" placeholder="{{ __('e.g. 100% Natural & Premium Quality') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional: Custom text for the badge above the hero title. Leave empty to use default.') }}</p>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Hero Title') }}</flux:label>
                    <flux:input wire:model="heroTitle" placeholder="{{ __('e.g. Premium Date Molasses') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional: Custom title for the hero section. Leave empty to use default hero section title.') }}</p>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Hero Content') }}</flux:label>
                    <flux:textarea wire:model="heroContent" rows="3" placeholder="{{ __('e.g. 100% Pure, Natural Sweetener. Rich in minerals and nutrients.') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional: Custom description text for the hero section. Leave empty to use default hero section content.') }}</p>
                </flux:field>
            </div>
        </section>

        <section class="space-y-6 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <flux:heading size="sm">{{ __('Page Layout') }}</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Drag to reorder, toggle to show or hide, and pick the content each section pulls from') }}</p>
                </div>
            </div>

            @php
                $blockLabels = [
                    'trust_badges' => __('Trust Badges'),
                    'product_details' => __('Product Details'),
                    'features' => __('Features'),
                    'testimonials' => __('Testimonials'),
                    'about' => __('About'),
                    'benefits' => __('Benefits'),
                    'faq' => __('FAQ'),
                    'cta' => __('Call to Action'),
                ];

                $blockPickers = [
                    'features' => ['items' => $featureSections, 'idsKey' => 'section_ids', 'search' => 'featureSectionSearch', 'labelKey' => 'title'],
                    'testimonials' => ['items' => $testimonials, 'idsKey' => 'testimonial_ids', 'search' => 'testimonialSearch', 'labelKey' => 'name'],
                    'about' => ['items' => $aboutSections, 'idsKey' => 'section_ids', 'search' => 'aboutSectionSearch', 'labelKey' => 'title'],
                    'benefits' => ['items' => $benefitsSections, 'idsKey' => 'section_ids', 'search' => 'benefitsSectionSearch', 'labelKey' => 'title'],
                    'faq' => ['items' => $faqSections, 'idsKey' => 'section_ids', 'search' => 'faqSectionSearch', 'labelKey' => 'title'],
                ];
            @endphp

            <div class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50/50 px-4 py-3 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span><strong>{{ __('Hero') }}</strong> — {{ __('always shown first, configured above') }}</span>
            </div>

            <div
                class="space-y-3"
                x-data="{
                    draggedType: null,

                    getBlockTypes() {
                        return Array.from(this.$el.querySelectorAll(':scope > [data-block-type]')).map(row => row.getAttribute('data-block-type'));
                    },

                    handleDragStart(event, type) {
                        this.draggedType = type;
                        event.dataTransfer.effectAllowed = 'move';
                        event.target.style.opacity = '0.5';
                    },

                    handleDragEnd(event) {
                        event.target.style.opacity = '1';
                        this.draggedType = null;
                    },

                    handleDragOver(event) {
                        event.preventDefault();
                        event.dataTransfer.dropEffect = 'move';
                    },

                    handleDrop(event, type) {
                        event.preventDefault();

                        if (this.draggedType === null || this.draggedType === type) {
                            return;
                        }

                        const types = this.getBlockTypes();
                        const oldIndex = types.indexOf(this.draggedType);
                        const newIndex = types.indexOf(type);

                        types.splice(oldIndex, 1);
                        types.splice(newIndex, 0, this.draggedType);

                        @this.call('updateBlockOrder', types);

                        this.draggedType = null;
                    }
                }"
            >
                @foreach($blocks as $index => $block)
                    <div
                        data-block-type="{{ $block['type'] }}"
                        draggable="true"
                        x-on:dragstart="handleDragStart($event, '{{ $block['type'] }}')"
                        x-on:dragend="handleDragEnd($event)"
                        x-on:dragover="handleDragOver($event)"
                        x-on:drop="handleDrop($event, '{{ $block['type'] }}')"
                        class="rounded-lg border border-zinc-200 bg-zinc-50/50 dark:border-zinc-700 dark:bg-zinc-800/50"
                        wire:key="block-{{ $block['type'] }}"
                    >
                        <div class="flex items-center gap-3 p-3">
                            <span class="cursor-move text-zinc-400 dark:text-zinc-500" aria-hidden="true">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 4a1 1 0 11-2 0 1 1 0 012 0zM7 10a1 1 0 11-2 0 1 1 0 012 0zM7 16a1 1 0 11-2 0 1 1 0 012 0zM15 4a1 1 0 11-2 0 1 1 0 012 0zM15 10a1 1 0 11-2 0 1 1 0 012 0zM15 16a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                            </span>
                            <span class="flex-1 text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $blockLabels[$block['type']] ?? $block['type'] }}</span>
                            <flux:switch wire:model="blocks.{{ $index }}.enabled" />
                        </div>

                        @if(isset($blockPickers[$block['type']]))
                            @php $picker = $blockPickers[$block['type']]; @endphp
                            <div class="border-t border-zinc-200 p-3 dark:border-zinc-700">
                                <flux:input wire:model.live.debounce.300ms="{{ $picker['search'] }}" placeholder="{{ __('Search...') }}" size="sm" class="mb-2" />
                                <div class="max-h-40 space-y-1 overflow-y-auto rounded-md border border-zinc-200 bg-white p-2 dark:border-zinc-700 dark:bg-zinc-900">
                                    @forelse($picker['items'] as $item)
                                        <div class="flex items-center gap-2 rounded-md p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                            <flux:checkbox
                                                wire:model="blocks.{{ $index }}.{{ $picker['idsKey'] }}"
                                                value="{{ (string) $item->id }}"
                                                label="{{ $picker['labelKey'] === 'name' ? $item->name.' - '.\Illuminate\Support\Str::limit($item->content, 40) : ($item->title_en ?? $item->title) }}"
                                            />
                                        </div>
                                    @empty
                                        <p class="p-2 text-xs text-zinc-500 dark:text-zinc-400">{{ __('None available — leave unchecked to show all active items of this type.') }}</p>
                                    @endforelse
                                </div>
                                <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Leave all unchecked to automatically show every active item of this type.') }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="space-y-6 rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                    <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="space-y-1">
                    <flux:heading size="sm">{{ __('Settings') }}</flux:heading>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Configure page status and display order') }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <flux:switch wire:model="is_active" label="{{ __('Active') }}" />
                    <flux:description class="mt-2">{{ __('Only active landing pages are accessible to visitors') }}</flux:description>
                </div>

                <flux:field>
                    <flux:label>{{ __('Display Order') }}</flux:label>
                    <flux:input type="number" wire:model="order" min="0" placeholder="0" />
                    <flux:description>{{ __('Lower numbers appear first in listings') }}</flux:description>
                    <flux:error name="order" />
                </flux:field>
            </div>
        </section>

        <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                <p class="font-medium">{{ __('Ready to create your landing page?') }}</p>
                <p class="text-xs">{{ __('Review your settings and click the button below to save') }}</p>
            </div>
            <div class="flex gap-3">
                <flux:button :href="route('admin.landing-pages.index')" wire:navigate variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ __('Create Landing Page') }}</span>
                    <span wire:loading wire:target="save">{{ __('Creating...') }}</span>
                </flux:button>
            </div>
        </div>
    </form>
</div>
