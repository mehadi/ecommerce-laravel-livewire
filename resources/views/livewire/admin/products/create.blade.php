<div class="w-full">
    {{-- Sticky action bar --}}
    <div class="sticky top-0 z-20 -mx-6 mb-8 border-b border-zinc-200 bg-white/90 px-6 py-4 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/90 lg:-mx-8 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" size="sm" square :aria-label="__('Back to Products')">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </flux:button>

                <div class="min-w-0 space-y-0.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:heading size="lg" class="truncate">{{ $isEdit ? __('Edit Product') : __('Create Product') }}</flux:heading>
                        <flux:badge size="sm" :color="$is_active ? 'emerald' : 'zinc'">
                            {{ $is_active ? __('Active') : __('Draft') }}
                        </flux:badge>
                        @if($is_featured)
                            <flux:badge size="sm" color="amber">{{ __('Featured') }}</flux:badge>
                        @endif
                    </div>
                    <flux:text size="sm" variant="subtle" class="hidden truncate sm:block">
                        {{ __('Craft a compelling product story with enriched descriptions, pricing clarity, and polished media.') }}
                    </flux:text>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" wire:loading.attr="disabled" wire:target="save">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="save" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ $isEdit ? __('Update Product') : __('Create Product') }}</span>
                    </span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $isEdit ? __('Updating...') : __('Creating...') }}</span>
                    </span>
                </flux:button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-6">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger" class="mb-6">{{ session('error') }}</flux:callout>
    @endif

    <form wire:submit="save" class="grid gap-8 lg:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-8 lg:col-span-2">
            <x-products.form-section
                :title="__('Product Overview')"
                :description="__('Set the essentials that shoppers see first across listings and detail pages.')"
                icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
            >
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Name (English)') }} *</flux:label>
                        <flux:input wire:model.live="name_en" :placeholder="__('Give your product a clear, benefit-led title')" />
                        <flux:error name="name_en" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Name (Bangla)') }}</flux:label>
                        <flux:input wire:model.live="name_bn" :placeholder="__('Localized title shown to Bangla-speaking shoppers')" />
                        <flux:error name="name_bn" />
                    </flux:field>
                </div>
            </x-products.form-section>

            <x-products.form-section
                :title="__('Description')"
                :description="__('Use rich text to highlight value, routines, and social proof.')"
                icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            >
                <div x-data="{ lang: 'en' }" class="space-y-6">
                    <div class="inline-flex rounded-lg border border-zinc-200 bg-zinc-100 p-1 dark:border-zinc-700 dark:bg-zinc-800" role="tablist">
                        <button
                            type="button"
                            role="tab"
                            @click="lang = 'en'"
                            :aria-selected="lang === 'en'"
                            :class="lang === 'en' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="cursor-pointer rounded-md px-3.5 py-1.5 text-sm font-medium transition-colors"
                        >
                            {{ __('English') }}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            @click="lang = 'bn'"
                            :aria-selected="lang === 'bn'"
                            :class="lang === 'bn' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="relative cursor-pointer rounded-md px-3.5 py-1.5 text-sm font-medium transition-colors"
                        >
                            {{ __('Bangla') }}
                            @if($errors->hasAny(['description_bn']))
                                <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-red-500"></span>
                            @endif
                        </button>
                    </div>

                    <div x-show="lang === 'en'" class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('Description (English)') }}</flux:label>
                            <div class="space-y-2" x-data="richTextEditor({ value: @entangle('description_en').live })" x-init="init()" data-rich-text>
                                <div wire:ignore>
                                    <input id="description_en_editor" type="hidden" x-ref="input">
                                    <trix-editor
                                        x-ref="editor"
                                        input="description_en_editor"
                                        placeholder="{{ __('Introduce benefits, usage rituals, and social proof.') }}"
                                    ></trix-editor>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Supports headings, lists, links, and keyboard shortcuts.') }}</p>
                            </div>
                            <flux:error name="description_en" />
                        </flux:field>
                    </div>

                    <div x-show="lang === 'bn'" class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('Description (Bangla)') }}</flux:label>
                            <div class="space-y-2" x-data="richTextEditor({ value: @entangle('description_bn').live })" x-init="init()" data-rich-text>
                                <div wire:ignore>
                                    <input id="description_bn_editor" type="hidden" x-ref="input">
                                    <trix-editor
                                        x-ref="editor"
                                        input="description_bn_editor"
                                        placeholder="{{ __('Localized storytelling to build stronger connections.') }}"
                                    ></trix-editor>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Mirror key talking points for Bangla-speaking customers.') }}</p>
                            </div>
                            <flux:error name="description_bn" />
                        </flux:field>
                    </div>
                </div>
            </x-products.form-section>

            {{-- Dynamic Attributes System --}}
            <x-products.form-section
                :title="__('Product Attributes')"
                :description="__('Use dynamic attributes like Color, Size, Weight to create product variations.')"
                icon="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            >
                <div class="space-y-6">
                    <div>
                        <flux:heading size="sm">{{ __('Select Attributes') }}</flux:heading>
                        <flux:text size="sm" variant="subtle">
                            {{ __('Choose which attributes this product will have (e.g., Color, Size, Weight)') }}
                        </flux:text>
                    </div>

                    @if($availableAttributes->isNotEmpty())
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($availableAttributes as $attribute)
                                @php
                                    $isAttrSelected = isset($selectedAttributes[$attribute->id]) && !empty($selectedAttributes[$attribute->id]);
                                @endphp
                                <div @class([
                                    'rounded-xl border p-4 transition-colors',
                                    'border-blue-300 bg-blue-50/60 dark:border-blue-500/40 dark:bg-blue-950/20' => $isAttrSelected,
                                    'border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800/60' => ! $isAttrSelected,
                                ])>
                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            <flux:heading size="sm">{{ $attribute->name }}</flux:heading>
                                            @if($attribute->unit)
                                                <flux:text size="xs" variant="subtle">({{ $attribute->unit }})</flux:text>
                                            @endif
                                        </div>
                                        <flux:checkbox
                                            wire:click="toggleAttribute({{ $attribute->id }})"
                                            :checked="$isAttrSelected"
                                        />
                                    </div>

                                    @if(isset($selectedAttributes[$attribute->id]))
                                        <div class="mt-3 space-y-1 border-t border-zinc-200/70 pt-3 dark:border-zinc-700/60">
                                            @foreach($attribute->activeValues as $value)
                                                @php
                                                    $isChecked = in_array($value->id, $selectedAttributes[$attribute->id] ?? []);
                                                @endphp
                                                <label class="flex cursor-pointer items-center gap-2 rounded-lg p-2 transition-colors hover:bg-white dark:hover:bg-zinc-700/60">
                                                    <input
                                                        type="checkbox"
                                                        wire:click="toggleAttributeValue({{ $attribute->id }}, {{ $value->id }})"
                                                        @if($isChecked) checked @endif
                                                        class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700"
                                                    >
                                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">
                                                        {{ $value->display_value ?: $value->value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <flux:callout variant="warning">
                            {{ __('No attributes available. Please create attributes first.') }}
                            <flux:button :href="route('admin.attributes.index')" wire:navigate variant="ghost" size="sm" class="mt-2">
                                {{ __('Go to Attributes') }}
                            </flux:button>
                        </flux:callout>
                    @endif

                    @if(!empty($productAttributes))
                        <div class="mt-2 space-y-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <flux:heading size="sm">{{ __('Attribute Combinations') }}</flux:heading>
                                    <flux:text size="sm" variant="subtle">
                                        {{ __('Set pricing, stock, and weight for each combination.') }}
                                    </flux:text>
                                </div>
                                <flux:badge size="sm" color="blue">
                                    {{ count($productAttributes) }} {{ count($productAttributes) === 1 ? __('combination') : __('combinations') }}
                                </flux:badge>
                            </div>

                            <div class="space-y-4">
                                @foreach($productAttributes as $combinationIndex => $combination)
                                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800/60" wire:key="combo-{{ $combinationIndex }}">
                                        <div class="mb-4 flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">{{ __('Variant') }} #{{ $combinationIndex + 1 }}</span>
                                            @foreach($combination['attribute_data'] ?? [] as $key => $value)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700/60 dark:text-zinc-200">
                                                    <span class="text-zinc-400 dark:text-zinc-500">{{ $key }}:</span> {{ $value }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <p class="mb-3 text-[11px] font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('Pricing') }}</p>
                                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                                            <flux:field>
                                                <flux:label>{{ __('Price') }} *</flux:label>
                                                <flux:input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model="productAttributes.{{ $combinationIndex }}.price"
                                                    min="0"
                                                    placeholder="0.00"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.price" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>{{ __('Compare At Price') }}</flux:label>
                                                <flux:input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model="productAttributes.{{ $combinationIndex }}.compare_at_price"
                                                    min="0"
                                                    placeholder="0.00"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.compare_at_price" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>{{ __('Buying Price') }}</flux:label>
                                                <flux:input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model="productAttributes.{{ $combinationIndex }}.buying_price"
                                                    min="0"
                                                    placeholder="0.00"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.buying_price" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>{{ __('Stock') }} *</flux:label>
                                                <flux:input
                                                    type="number"
                                                    wire:model="productAttributes.{{ $combinationIndex }}.stock"
                                                    min="0"
                                                    placeholder="0"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.stock" />
                                            </flux:field>
                                        </div>

                                        <p class="mb-3 mt-5 text-[11px] font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('Inventory Details') }}</p>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <flux:field>
                                                <flux:label>{{ __('Weight (kg)') }}</flux:label>
                                                <flux:input
                                                    type="number"
                                                    step="0.01"
                                                    wire:model="productAttributes.{{ $combinationIndex }}.weight_kg"
                                                    min="0"
                                                    placeholder="Auto from attribute"
                                                />
                                                <flux:description>{{ __('Leave empty to use weight from attribute') }}</flux:description>
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.weight_kg" />
                                            </flux:field>

                                            <flux:field>
                                                <flux:label>{{ __('SKU') }}</flux:label>
                                                <flux:input
                                                    wire:model="productAttributes.{{ $combinationIndex }}.sku"
                                                    :placeholder="__('Optional unique SKU')"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.sku" />
                                            </flux:field>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-products.form-section>

            <x-products.form-section
                :title="__('Pricing & Availability')"
                :description="__('Define selling price, promotional anchors, and inventory visibility.')"
                icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            >
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>
                            <div class="flex items-center justify-between">
                                <span>{{ __('SKU') }}</span>
                                @if($name_en && !$sku)
                                    <flux:button type="button" wire:click="generateSku" size="sm" variant="ghost" class="text-xs">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span>{{ __('Auto-generate') }}</span>
                                        </span>
                                    </flux:button>
                                @endif
                            </div>
                        </flux:label>
                        <flux:input wire:model.blur="sku" :placeholder="__('e.g. SKU-00123')" />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Use a unique, searchable identifier for inventory syncing.') }}</p>
                        <flux:error name="sku" />
                    </flux:field>

                    @if(empty($productAttributes))
                        <flux:field>
                            <flux:label>{{ __('Stock') }} *</flux:label>
                            <flux:input type="number" wire:model="stock" min="0" :disabled="$tracks_batches" :placeholder="__('Available units ready to sell')" />
                            @if($tracks_batches)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Batch-tracked — add stock via batches in Inventory once this product is saved.') }}</p>
                            @endif
                            <flux:error name="stock" />
                        </flux:field>

                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Track Batches / Lots') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enable for products with expiry dates or lot numbers you need to trace.') }}</p>
                            </div>
                            <flux:switch wire:model.live="tracks_batches" />
                        </div>
                    @else
                        <div class="flex items-end pb-1">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Stock is tracked per variation above.') }}</p>
                        </div>
                    @endif

                    <flux:field>
                        <flux:label>{{ __('Low Stock Alert Threshold') }}</flux:label>
                        <flux:input type="number" wire:model="low_stock_threshold" min="1" :placeholder="__('Store default (:threshold)', ['threshold' => \App\Models\Setting::get('low_stock_threshold', '10')])" />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Leave blank to use the store-wide default set in Inventory.') }}</p>
                        <flux:error name="low_stock_threshold" />
                    </flux:field>
                </div>

                @if(empty($productAttributes))
                    <x-products.pricing-section
                        price="price"
                        compareAtPrice="compare_at_price"
                        buyingPrice="buying_price"
                        :profit="$this->profit"
                        :profitPercentage="$this->profitPercentage"
                        :currency="\App\Models\Setting::get('currency_symbol', '৳')"
                    />
                @else
                    <div class="flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <svg class="h-5 w-5 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <flux:text size="sm" variant="subtle">
                            {{ __('Pricing and stock are managed per variation combination above.') }}
                        </flux:text>
                    </div>
                @endif
            </x-products.form-section>

            <x-products.form-section
                :title="__('Media Library')"
                :description="__('Upload polished visuals to reinforce credibility and conversion.')"
                icon="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            >
                <div class="grid gap-6 lg:grid-cols-2">
                    <flux:field class="space-y-4">
                        <flux:label>{{ __('Primary Image') }}</flux:label>

                        @if($isEdit && $product && $product->primary_image)
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ asset('storage/'.$product->primary_image) }}" class="h-32 w-32 rounded-lg object-cover" alt="{{ __('Current primary image') }}">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Current storefront image') }}</span>
                            </div>
                        @endif

                        <x-media.drag-drop-uploader
                            wire-model="primary_image"
                            :value="$primary_image"
                            remove-method="removePrimaryImage"
                            :placeholder-title="__('Drag & drop your hero image')"
                            :placeholder-description="__('We recommend crisp, high-resolution visuals to showcase the product.')"
                            :button-text="__('Browse files')"
                            :helper-text="__('PNG, JPG or WebP - Max 2MB')"
                            :badge-text="__('Primary')"
                            :preview-helper="__('Drop a new file or click to replace the hero image.')"
                            :footnote="__('Recommended 1200x1200px for best storefront coverage.')"
                            :secondary-footnote="__('Supports transparency and square crops.')"
                            :loading-text="__('Uploading primary image...')"
                        />

                        <flux:error name="primary_image" />
                    </flux:field>

                    <flux:field class="space-y-4">
                        <flux:label>{{ __('Gallery Images') }}</flux:label>

                        @if($isEdit && !empty($existing_gallery))
                            <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach($existing_gallery as $index => $image)
                                    <div class="group relative overflow-hidden rounded-lg" wire:key="existing-gallery-{{ $index }}">
                                        <img src="{{ asset('storage/'.$image) }}" class="h-28 w-full rounded-lg object-cover" alt="{{ __('Existing gallery image :number', ['number' => $loop->iteration]) }}">
                                        <button
                                            type="button"
                                            wire:click="removeExistingGalleryImage({{ $index }})"
                                            class="absolute right-2 top-2 inline-flex items-center justify-center rounded-full bg-zinc-900/75 p-1.5 text-white backdrop-blur transition hover:bg-red-600/90"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="sr-only">{{ __('Remove image') }}</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <x-media.drag-drop-uploader
                            wire-model="gallery_images"
                            :value="$gallery_images"
                            :multiple="true"
                            remove-method="removeGalleryImage"
                            :placeholder-title="__('Drag in lifestyle or detail shots')"
                            :placeholder-description="__('Drop multiple files at once to build rich galleries in seconds.')"
                            :button-text="__('Add images')"
                            :helper-text="__('PNG, JPG or WebP - Up to 8 images per upload')"
                            icon-classes="bg-purple-100 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400"
                            highlight-class="border-purple-500/80 bg-purple-50/80 dark:border-purple-400/60 dark:bg-purple-900/20"
                            hover-class="hover:border-purple-500 hover:bg-purple-50/80 dark:hover:border-purple-400/60 dark:hover:bg-purple-900/30"
                            :empty-hint="__('Drag and drop product angles to craft a compelling story.')"
                            :loading-text="__('Uploading gallery images...')"
                            loading-class="text-purple-600 dark:text-purple-400"
                        />

                        <flux:error name="gallery_images.*" />
                    </flux:field>
                </div>
            </x-products.form-section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8 lg:sticky lg:top-24 lg:col-span-1 lg:self-start">
            <x-products.form-section
                :title="__('Status')"
                :description="__('Control storefront visibility and merchandising.')"
                icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            >
                <div class="space-y-5">
                    <flux:switch wire:model.live="is_active" :label="__('Active')" :description="__('Visible to shoppers on your storefront.')" />
                    <div class="h-px bg-zinc-100 dark:bg-zinc-800"></div>
                    <flux:switch wire:model.live="is_featured" :label="__('Featured')" :description="__('Highlight on the homepage and curated collections.')" />
                </div>
            </x-products.form-section>

            <x-products.form-section
                :title="__('Organization')"
                :description="__('Control how this product is grouped and ordered.')"
                icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
            >
                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <flux:select wire:model="category_id">
                            <option value="">{{ __('No Category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->parent ? $category->parent->name_en . ' > ' : '' }}{{ $category->name_en }}</option>
                            @endforeach
                        </flux:select>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional. Helps customers browse by collection.') }}</p>
                        <flux:error name="category_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Default Supplier') }}</flux:label>
                        <flux:select wire:model="default_supplier_id">
                            <option value="">{{ __('No Default Supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </flux:select>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional. Used to group this product under Inventory > Suggested Reorders.') }}</p>
                        <flux:error name="default_supplier_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Sort Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" :placeholder="__('Control featured ranking or manual merchandising.')" />
                        <flux:error name="order" />
                    </flux:field>
                </div>
            </x-products.form-section>
        </div>

        {{-- Bottom actions --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700 lg:col-span-3">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ $isEdit ? __('Update Product') : __('Create Product') }}</span>
                </span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ $isEdit ? __('Updating...') : __('Creating...') }}</span>
                </span>
            </flux:button>
            <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" wire:loading.attr="disabled" wire:target="save">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</div>
