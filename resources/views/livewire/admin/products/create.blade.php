<div
    class="w-full"
    x-data="{ dirty: false, saving: false }"
    x-on:input="dirty = true"
    x-on:change="dirty = true"
    x-on:trix-change="dirty = true"
    x-on:product-form-invalid.window="saving = false; $nextTick(() => document.getElementById('product-form-errors')?.scrollIntoView({ behavior: 'smooth', block: 'center' }))"
    x-init="
        const beforeUnload = (event) => {
            if (dirty && ! saving) {
                event.preventDefault();
                event.returnValue = '';
            }
        };
        const navigateGuard = (event) => {
            if (! document.body.contains($el)) {
                document.removeEventListener('livewire:navigate', navigateGuard);
                window.removeEventListener('beforeunload', beforeUnload);
                return;
            }
            if (dirty && ! saving && ! confirm('{{ __('You have unsaved changes. Leave this page and discard them?') }}')) {
                event.preventDefault();
            }
        };
        window.addEventListener('beforeunload', beforeUnload);
        document.addEventListener('livewire:navigate', navigateGuard);
    "
>
    {{-- Action bar: static on phones (viewport is too precious), sticky from sm up --}}
    <div class="z-20 -mx-6 mb-8 border-b border-zinc-200 bg-white/90 px-6 py-4 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/90 sm:sticky sm:top-0 lg:-mx-8 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" size="sm" square :aria-label="__('Back to Products')">
                    <flux:icon.arrow-left class="size-4" aria-hidden="true" />
                </flux:button>

                <div class="min-w-0 space-y-0.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:heading size="lg" class="truncate">{{ $isEdit ? __('Edit Product') : __('Create Product') }}</flux:heading>
                        <div class="hidden items-center gap-2 sm:flex">
                            <flux:badge size="sm" :color="$is_active ? 'emerald' : 'zinc'">
                                {{ $is_active ? __('Active') : __('Draft') }}
                            </flux:badge>
                            @if($is_featured)
                                <flux:badge size="sm" color="amber">{{ __('Featured') }}</flux:badge>
                            @endif
                        </div>
                    </div>
                    <flux:text size="sm" variant="subtle" class="hidden truncate sm:block">
                        {{ __('Craft a compelling product story with enriched descriptions, pricing clarity, and polished media.') }}
                    </flux:text>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if($isEdit)
                    <flux:button
                        type="button"
                        wire:click="duplicate"
                        wire:confirm="{{ __('Duplicate this product as an inactive draft copy? Stock, SKUs and barcodes are not copied.') }}"
                        variant="ghost"
                        wire:loading.attr="disabled"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <flux:icon.document-duplicate class="size-4" aria-hidden="true" />
                            <span class="hidden md:inline">{{ __('Duplicate') }}</span>
                            <span class="sr-only md:hidden">{{ __('Duplicate product') }}</span>
                        </span>
                    </flux:button>
                @endif
                <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" class="hidden sm:inline-flex" wire:loading.attr="disabled" wire:target="save">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="save" x-on:click="saving = true" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">
                        <flux:icon.check class="size-4" aria-hidden="true" />
                        <span>{{ $isEdit ? __('Update Product') : __('Create Product') }}</span>
                    </span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                        <flux:icon.arrow-path class="size-4 animate-spin" aria-hidden="true" />
                        <span>{{ $isEdit ? __('Updating...') : __('Creating...') }}</span>
                    </span>
                </flux:button>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div role="status" aria-live="polite">
            <flux:callout variant="success" class="mb-6">{{ session('message') }}</flux:callout>
        </div>
    @endif

    @if (session()->has('error'))
        <div role="alert" aria-live="assertive">
            <flux:callout variant="danger" class="mb-6">{{ session('error') }}</flux:callout>
        </div>
    @endif

    @if ($errors->any())
        <div id="product-form-errors" role="alert" aria-live="assertive">
            <flux:callout variant="danger" class="mb-6">
                {{ trans_choice('One field needs attention — review the highlighted input below.|:count fields need attention — review the highlighted inputs below.', $errors->count(), ['count' => $errors->count()]) }}
            </flux:callout>
        </div>
    @endif

    <form wire:submit="save" x-on:submit="saving = true" class="grid gap-8 lg:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-8 lg:col-span-2">
            <x-products.form-section
                :title="__('Product Overview')"
                :description="__('Set the essentials that shoppers see first across listings and detail pages.')"
                icon="cube"
            >
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Name (English)') }} *</flux:label>
                        <flux:input wire:model.blur="name_en" :placeholder="__('Give your product a clear, benefit-led title')" />
                        <flux:error name="name_en" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Name (Bangla)') }}</flux:label>
                        <flux:input wire:model.blur="name_bn" :placeholder="__('Localized title shown to Bangla-speaking shoppers')" />
                        <flux:error name="name_bn" />
                    </flux:field>
                </div>
            </x-products.form-section>

            <x-products.form-section
                :title="__('Description')"
                :description="__('Use rich text to highlight value, routines, and social proof.')"
                icon="pencil-square"
            >
                <div x-data="{ lang: 'en' }" class="space-y-6">
                    <div class="inline-flex rounded-lg border border-zinc-200 bg-zinc-100 p-1 dark:border-zinc-700 dark:bg-zinc-800" role="tablist" aria-label="{{ __('Description language') }}">
                        <button
                            type="button"
                            role="tab"
                            id="description-tab-en"
                            aria-controls="description-panel-en"
                            @click="lang = 'en'"
                            @keydown.arrow-right.prevent="lang = 'bn'; $nextTick(() => document.getElementById('description-tab-bn')?.focus())"
                            @keydown.arrow-left.prevent="lang = 'bn'; $nextTick(() => document.getElementById('description-tab-bn')?.focus())"
                            :aria-selected="lang === 'en' ? 'true' : 'false'"
                            :tabindex="lang === 'en' ? 0 : -1"
                            :class="lang === 'en' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="cursor-pointer rounded-md px-3.5 py-1.5 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            {{ __('English') }}
                        </button>
                        <button
                            type="button"
                            role="tab"
                            id="description-tab-bn"
                            aria-controls="description-panel-bn"
                            @click="lang = 'bn'"
                            @keydown.arrow-right.prevent="lang = 'en'; $nextTick(() => document.getElementById('description-tab-en')?.focus())"
                            @keydown.arrow-left.prevent="lang = 'en'; $nextTick(() => document.getElementById('description-tab-en')?.focus())"
                            :aria-selected="lang === 'bn' ? 'true' : 'false'"
                            :tabindex="lang === 'bn' ? 0 : -1"
                            :class="lang === 'bn' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
                            class="relative cursor-pointer rounded-md px-3.5 py-1.5 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            {{ __('Bangla') }}
                            @if($errors->hasAny(['description_bn']))
                                <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-red-500" aria-hidden="true"></span>
                                <span class="sr-only">{{ __('has validation errors') }}</span>
                            @endif
                        </button>
                    </div>

                    <div x-show="lang === 'en'" role="tabpanel" id="description-panel-en" aria-labelledby="description-tab-en" tabindex="0" class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('Description (English)') }}</flux:label>
                            <div class="space-y-2" x-data="richTextEditor({ value: @entangle('description_en') })" x-init="init()" data-rich-text>
                                <div wire:ignore>
                                    <input id="description_en_editor" type="hidden" x-ref="input">
                                    <trix-editor
                                        x-ref="editor"
                                        input="description_en_editor"
                                        aria-label="{{ __('Description (English)') }}"
                                        placeholder="{{ __('Introduce benefits, usage rituals, and social proof.') }}"
                                    ></trix-editor>
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Supports headings, lists, links, and keyboard shortcuts.') }}</p>
                            </div>
                            <flux:error name="description_en" />
                        </flux:field>
                    </div>

                    <div x-show="lang === 'bn'" role="tabpanel" id="description-panel-bn" aria-labelledby="description-tab-bn" tabindex="0" class="space-y-6">
                        <flux:field>
                            <flux:label>{{ __('Description (Bangla)') }}</flux:label>
                            <div class="space-y-2" x-data="richTextEditor({ value: @entangle('description_bn') })" x-init="init()" data-rich-text>
                                <div wire:ignore>
                                    <input id="description_bn_editor" type="hidden" x-ref="input">
                                    <trix-editor
                                        x-ref="editor"
                                        input="description_bn_editor"
                                        aria-label="{{ __('Description (Bangla)') }}"
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
                icon="swatch"
            >
                <div class="space-y-6">
                    <div>
                        <flux:heading size="sm">{{ __('Select Attributes') }}</flux:heading>
                        <flux:text size="sm" variant="subtle">
                            {{ __('Choose which attributes this product will have (e.g., Color, Size, Weight)') }}
                        </flux:text>
                    </div>

                    @if($this->availableAttributes->isNotEmpty())
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->availableAttributes as $attribute)
                                @php
                                    $isAttrSelected = isset($selectedAttributes[$attribute->id]) && !empty($selectedAttributes[$attribute->id]);
                                @endphp
                                <div wire:key="attr-card-{{ $attribute->id }}" @class([
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
                                            wire:loading.attr="disabled"
                                            wire:target="toggleAttribute, toggleAttributeValue"
                                            :checked="$isAttrSelected"
                                            :aria-label="__('Toggle :name attribute', ['name' => $attribute->name])"
                                        />
                                    </div>

                                    @if(isset($selectedAttributes[$attribute->id]))
                                        <div class="mt-3 space-y-1 border-t border-zinc-200/70 pt-3 dark:border-zinc-700/60">
                                            @foreach($attribute->activeValues as $value)
                                                @php
                                                    $isChecked = in_array($value->id, $selectedAttributes[$attribute->id] ?? []);
                                                @endphp
                                                <label wire:key="attr-value-{{ $attribute->id }}-{{ $value->id }}" class="flex min-h-11 cursor-pointer items-center gap-2.5 rounded-lg p-2.5 transition-colors hover:bg-white dark:hover:bg-zinc-700/60">
                                                    <input
                                                        type="checkbox"
                                                        wire:click="toggleAttributeValue({{ $attribute->id }}, {{ $value->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="toggleAttribute, toggleAttributeValue"
                                                        @if($isChecked) checked @endif
                                                        class="size-5 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700"
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
                                        {{ __('Set pricing, stock, and weight for each combination. Values you have already entered are kept when you add or remove options.') }}
                                    </flux:text>
                                </div>
                                <flux:badge size="sm" color="blue">
                                    {{ count($productAttributes) }} {{ count($productAttributes) === 1 ? __('combination') : __('combinations') }}
                                </flux:badge>
                            </div>

                            {{-- Bulk tools --}}
                            <div class="flex flex-wrap items-end gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                                <flux:field class="w-32">
                                    <flux:label class="text-xs">{{ __('Price for all') }}</flux:label>
                                    <flux:input type="number" step="0.01" min="0" wire:model="bulkVariantPrice" placeholder="0.00" size="sm" />
                                </flux:field>
                                <flux:button type="button" size="sm" variant="outline" wire:click="applyBulkVariantPrice" wire:loading.attr="disabled">
                                    {{ __('Apply') }}
                                </flux:button>

                                <div class="h-9 w-px bg-zinc-200 dark:bg-zinc-700" aria-hidden="true"></div>

                                <flux:field class="w-32">
                                    <flux:label class="text-xs">{{ __('Stock for all') }}</flux:label>
                                    <flux:input type="number" min="0" wire:model="bulkVariantStock" placeholder="0" size="sm" />
                                </flux:field>
                                <flux:button type="button" size="sm" variant="outline" wire:click="applyBulkVariantStock" wire:loading.attr="disabled">
                                    {{ __('Apply') }}
                                </flux:button>

                                <div class="h-9 w-px bg-zinc-200 dark:bg-zinc-700" aria-hidden="true"></div>

                                <flux:button type="button" size="sm" variant="outline" wire:click="generateVariantSkus" wire:loading.attr="disabled">
                                    <span class="inline-flex items-center gap-1.5">
                                        <flux:icon.sparkles class="size-3.5" aria-hidden="true" />
                                        {{ __('Fill empty SKUs') }}
                                    </span>
                                </flux:button>
                            </div>

                            <div class="space-y-4">
                                @foreach($productAttributes as $combinationIndex => $combination)
                                    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800/60" wire:key="combo-{{ md5(json_encode($combination['attribute_data'] ?? [])) }}">
                                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('Variant') }} #{{ $combinationIndex + 1 }}</span>
                                                @foreach($combination['attribute_data'] ?? [] as $key => $value)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700/60 dark:text-zinc-200">
                                                        <span class="text-zinc-500 dark:text-zinc-400">{{ $key }}:</span> {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <flux:switch
                                                wire:model="productAttributes.{{ $combinationIndex }}.is_active"
                                                :label="__('Active')"
                                            />
                                        </div>

                                        <p class="mb-3 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Pricing') }}</p>
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

                                        <p class="mb-3 mt-5 text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Inventory Details') }}</p>
                                        <div class="grid gap-4 md:grid-cols-3">
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

                                            <flux:field>
                                                <flux:label>{{ __('Barcode') }}</flux:label>
                                                <flux:input
                                                    wire:model="productAttributes.{{ $combinationIndex }}.barcode"
                                                    :placeholder="__('Scanned at the POS terminal')"
                                                />
                                                <flux:error name="productAttributes.{{ $combinationIndex }}.barcode" />
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
                icon="currency-dollar"
            >
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>
                            <div class="flex items-center justify-between">
                                <span>{{ __('SKU') }}</span>
                                @if($name_en && !$sku)
                                    <flux:button type="button" wire:click="generateSku" size="sm" variant="ghost" class="text-xs">
                                        <span class="inline-flex items-center gap-1">
                                            <flux:icon.arrow-path class="size-3" aria-hidden="true" />
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

                    <flux:field>
                        <flux:label>{{ __('Barcode') }}</flux:label>
                        <flux:input wire:model.blur="barcode" :placeholder="__('e.g. EAN/UPC from the packaging')" />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Lets the POS terminal add this product with a scan.') }}</p>
                        <flux:error name="barcode" />
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

                        <div class="flex items-center justify-between gap-3 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('Track Batches / Lots') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enable for products with expiry dates or lot numbers you need to trace.') }}</p>
                            </div>
                            <flux:switch wire:model.live="tracks_batches" :aria-label="__('Track batches / lots')" />
                        </div>
                    @else
                        <div class="flex items-end pb-1 md:col-span-1">
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
                        :currency="\App\Models\Setting::get('currency_symbol', '৳')"
                    />
                @else
                    <div class="flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <flux:icon.information-circle class="size-5 shrink-0 text-zinc-400" aria-hidden="true" />
                        <flux:text size="sm" variant="subtle">
                            {{ __('Pricing and stock are managed per variation combination above.') }}
                        </flux:text>
                    </div>
                @endif
            </x-products.form-section>

            <x-products.form-section
                :title="__('Media Library')"
                :description="__('Upload polished visuals to reinforce credibility and conversion.')"
                icon="photo"
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
                            :label="__('Upload primary image')"
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
                            <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach($existing_gallery as $index => $image)
                                    <div class="group relative overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700" wire:key="existing-gallery-{{ md5($image) }}">
                                        <img src="{{ asset('storage/'.$image) }}" class="h-28 w-full object-cover" alt="{{ __('Existing gallery image :number', ['number' => $loop->iteration]) }}">

                                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 bg-gradient-to-t from-zinc-900/80 to-transparent p-1.5">
                                            <button
                                                type="button"
                                                wire:click="moveExistingGalleryImage({{ $index }}, -1)"
                                                @disabled($index === 0)
                                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-full text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white disabled:opacity-30"
                                            >
                                                <flux:icon.chevron-left class="size-4" aria-hidden="true" />
                                                <span class="sr-only">{{ __('Move image :number earlier', ['number' => $loop->iteration]) }}</span>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="makeGalleryImagePrimary({{ $index }})"
                                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-full text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                            >
                                                <flux:icon.star class="size-4" aria-hidden="true" />
                                                <span class="sr-only">{{ __('Make image :number the primary image', ['number' => $loop->iteration]) }}</span>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="moveExistingGalleryImage({{ $index }}, 1)"
                                                @disabled($loop->last)
                                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-full text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white disabled:opacity-30"
                                            >
                                                <flux:icon.chevron-right class="size-4" aria-hidden="true" />
                                                <span class="sr-only">{{ __('Move image :number later', ['number' => $loop->iteration]) }}</span>
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeExistingGalleryImage({{ $index }})"
                                            wire:confirm="{{ __('Remove this image? The file is deleted immediately.') }}"
                                            class="absolute right-1.5 top-1.5 inline-flex min-h-9 min-w-9 items-center justify-center rounded-full bg-zinc-900/75 text-white backdrop-blur transition hover:bg-red-600/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
                                        >
                                            <flux:icon.x-mark class="size-4" aria-hidden="true" />
                                            <span class="sr-only">{{ __('Remove image :number', ['number' => $loop->iteration]) }}</span>
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
                            :label="__('Upload gallery images')"
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

        {{-- Sidebar: ordered first on mobile so Status/Organization (high-frequency
             fields when editing) don't require scrolling past the whole form --}}
        <div class="space-y-8 max-lg:order-first lg:sticky lg:top-24 lg:col-span-1 lg:self-start">
            <x-products.form-section
                :title="__('Status')"
                :description="__('Control storefront visibility and merchandising.')"
                icon="check-circle"
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
                icon="tag"
            >
                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <flux:select wire:model="category_id">
                            <option value="">{{ __('No Category') }}</option>
                            @foreach($this->categories as $category)
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
                            @foreach($this->suppliers as $supplier)
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

            @php
                $completeness = [
                    __('Product name') => filled($name_en),
                    __('Description') => filled($description_en) || filled($description_bn),
                    __('Primary image') => (bool) ($primary_image || $product?->primary_image),
                    __('Category') => filled($category_id),
                    __('Pricing') => ! empty($productAttributes) || (float) $price > 0,
                    __('SKU') => filled($sku),
                ];
                $completedCount = count(array_filter($completeness));
            @endphp
            <x-products.form-section
                :title="__('Completeness')"
                :description="__('Products with complete details convert better and are easier to find.')"
                icon="clipboard-document-check"
            >
                <x-slot:badge>
                    <flux:badge size="sm" :color="$completedCount === count($completeness) ? 'emerald' : 'zinc'">
                        {{ $completedCount }}/{{ count($completeness) }}
                    </flux:badge>
                </x-slot:badge>

                <ul class="space-y-2.5">
                    @foreach($completeness as $label => $done)
                        <li class="flex items-center gap-2.5 text-sm">
                            @if($done)
                                <flux:icon.check-circle class="size-5 shrink-0 text-emerald-500" aria-hidden="true" />
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
                                <span class="sr-only">{{ __('complete') }}</span>
                            @else
                                <flux:icon.minus-circle class="size-5 shrink-0 text-zinc-300 dark:text-zinc-600" aria-hidden="true" />
                                <span class="text-zinc-500 dark:text-zinc-400">{{ $label }}</span>
                                <span class="sr-only">{{ __('missing') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-products.form-section>
        </div>

        {{-- Bottom actions --}}
        <div class="flex flex-wrap items-center gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700 lg:col-span-3">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">
                    <flux:icon.check class="size-4" aria-hidden="true" />
                    <span>{{ $isEdit ? __('Update Product') : __('Create Product') }}</span>
                </span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                    <flux:icon.arrow-path class="size-4 animate-spin" aria-hidden="true" />
                    <span>{{ $isEdit ? __('Updating...') : __('Creating...') }}</span>
                </span>
            </flux:button>
            <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" wire:loading.attr="disabled" wire:target="save">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
</div>
