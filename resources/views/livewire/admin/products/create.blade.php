<div class="w-full space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-1">
            <flux:heading size="lg">{{ $isEdit ? __('Edit Product') : __('Create Product') }}</flux:heading>
            <flux:text size="sm" variant="subtle">
                {{ __('Craft a compelling product story with enriched descriptions, pricing clarity, and polished media.') }}
            </flux:text>
        </div>

        <flux:button :href="route('admin.products.index')" wire:navigate variant="ghost" size="sm">
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Back to Products') }}</span>
            </span>
        </flux:button>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    <form wire:submit="save" class="space-y-8">
        <x-products.form-section 
            title="{{ __('Product Overview') }}"
            description="{{ __('Set the essentials that shoppers see first across listings and detail pages.') }}"
        >

            <div class="grid gap-6 md:grid-cols-2">
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
                    <flux:input wire:model.blur="sku" placeholder="{{ __('e.g. SKU-00123') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Use a unique, searchable identifier for inventory syncing.') }}</p>
                    <flux:error name="sku" />
                </flux:field>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Name (English)') }} *</flux:label>
                    <flux:input wire:model.live="name_en" placeholder="{{ __('Give your product a clear, benefit-led title') }}" />
                    <flux:error name="name_en" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Name (Bangla)') }}</flux:label>
                    <flux:input wire:model.live="name_bn" placeholder="{{ __('Localized title shown to Bangla-speaking shoppers') }}" />
                    <flux:error name="name_bn" />
                </flux:field>
            </div>
        </x-products.form-section>

        <x-products.form-section 
            title="{{ __('Story & Ingredients') }}"
            description="{{ __('Use rich text to highlight value, routines, and ingredient transparency.') }}"
        >

            <div class="grid gap-6">
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

            <div class="grid gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Ingredients (English)') }}</flux:label>
                    <flux:textarea wire:model="ingredients_en" rows="4" placeholder="{{ __('List standout ingredients and sourcing details.') }}" />
                    <flux:error name="ingredients_en" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Ingredients (Bangla)') }}</flux:label>
                    <flux:textarea wire:model="ingredients_bn" rows="4" placeholder="{{ __('Translate ingredient highlights to build trust.') }}" />
                    <flux:error name="ingredients_bn" />
                </flux:field>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Benefits (English)') }}</flux:label>
                    <flux:textarea wire:model="benefits_en" rows="4" placeholder="{{ __('Explain outcomes, routines, or usage tips.') }}" />
                    <flux:error name="benefits_en" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Benefits (Bangla)') }}</flux:label>
                    <flux:textarea wire:model="benefits_bn" rows="4" placeholder="{{ __('Translate benefits to resonate with Bangla audiences.') }}" />
                    <flux:error name="benefits_bn" />
                </flux:field>
            </div>
        </x-products.form-section>

        {{-- New Dynamic Attributes System --}}
        <x-products.form-section 
            title="{{ __('Product Attributes') }}"
            description="{{ __('Use dynamic attributes like Color, Size, Weight to create product variations.') }}"
        >
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="sm">{{ __('Select Attributes') }}</flux:heading>
                        <flux:text size="sm" variant="subtle">
                            {{ __('Choose which attributes this product will have (e.g., Color, Size, Weight)') }}
                        </flux:text>
                    </div>
                </div>

                @if(!empty($attributes))
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($attributes as $attribute)
                            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <flux:heading size="sm">{{ $attribute->name }}</flux:heading>
                                        @if($attribute->unit)
                                            <flux:text size="xs" variant="subtle">({{ $attribute->unit }})</flux:text>
                                        @endif
                                    </div>
                                    <flux:checkbox 
                                        wire:click="toggleAttribute({{ $attribute->id }})"
                                        :checked="isset($selectedAttributes[$attribute->id]) && !empty($selectedAttributes[$attribute->id])"
                                    />
                                </div>
                                
                                @if(isset($selectedAttributes[$attribute->id]))
                                    <div class="space-y-2 mt-3">
                                        @foreach($attribute->activeValues as $value)
                                            @php
                                                $isChecked = isset($selectedAttributes[$attribute->id]) && in_array($value->id, $selectedAttributes[$attribute->id] ?? []);
                                            @endphp
                                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                <input 
                                                    type="checkbox" 
                                                    wire:click="toggleAttributeValue({{ $attribute->id }}, {{ $value->id }})"
                                                    @if($isChecked) checked @endif
                                                    class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="text-sm text-gray-700 dark:text-gray-300">
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
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:heading size="sm">{{ __('Attribute Combinations') }}</flux:heading>
                                <flux:text size="sm" variant="subtle">
                                    {{ __('Set pricing, stock, and weight for each combination.') }}
                                </flux:text>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($productAttributes as $combinationIndex => $combination)
                                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800/60">
                                    <div class="mb-4">
                                        <flux:badge variant="solid" class="mb-2">
                                            @foreach($combination['attribute_data'] ?? [] as $key => $value)
                                                {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                            @endforeach
                                        </flux:badge>
                                    </div>

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

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
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
                                                placeholder="{{ __('Optional unique SKU') }}"
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
            title="{{ __('Pricing & Availability') }}"
            description="{{ __('Define selling price, promotional anchors, and inventory visibility.') }}"
        >
            @if(empty($productAttributes))
                <x-products.pricing-section
                    price="price"
                    compareAtPrice="compare_at_price"
                    buyingPrice="buying_price"
                    :profit="$this->profit"
                    :profitPercentage="$this->profitPercentage"
                />

                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Stock') }} *</flux:label>
                        <flux:input type="number" wire:model="stock" min="0" placeholder="{{ __('Available units ready to sell') }}" />
                        <flux:error name="stock" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Sort Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" placeholder="{{ __('Control featured ranking or manual merchandising.') }}" />
                        <flux:error name="order" />
                    </flux:field>
                </div>
            @else
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-800/60">
                    <flux:text size="sm" variant="subtle">
                        {{ __('Pricing and stock are managed per variation combination above.') }}
                    </flux:text>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Sort Order') }}</flux:label>
                        <flux:input type="number" wire:model="order" min="0" placeholder="{{ __('Control featured ranking or manual merchandising.') }}" />
                        <flux:error name="order" />
                    </flux:field>
                </div>
            @endif
        </x-products.form-section>

        <x-products.form-section 
            title="{{ __('Media Library') }}"
            description="{{ __('Upload polished visuals to reinforce credibility and conversion.') }}"
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
                        placeholder-title="{{ __('Drag & drop your hero image') }}"
                        placeholder-description="{{ __('We recommend crisp, high-resolution visuals to showcase the product.') }}"
                        button-text="{{ __('Browse files') }}"
                        helper-text="{{ __('PNG, JPG or WebP - Max 2MB') }}"
                        badge-text="{{ __('Primary') }}"
                        preview-helper="{{ __('Drop a new file or click to replace the hero image.') }}"
                        footnote="{{ __('Recommended 1200x1200px for best storefront coverage.') }}"
                        secondary-footnote="{{ __('Supports transparency and square crops.') }}"
                        loading-text="{{ __('Uploading primary image...') }}"
                    />

                    <flux:error name="primary_image" />
                </flux:field>

                <flux:field class="space-y-4">
                    <flux:label>{{ __('Gallery Images') }}</flux:label>

                    @if($isEdit && !empty($existing_gallery))
                        <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach($existing_gallery as $index => $image)
                                <div class="group relative" wire:key="existing-gallery-{{ $index }}">
                                    <img src="{{ asset('storage/'.$image) }}" class="h-28 w-full rounded-lg object-cover" alt="{{ __('Existing gallery image :number', ['number' => $loop->iteration]) }}">
                                    <button
                                        type="button"
                                        wire:click="removeExistingGalleryImage({{ $index }})"
                                        class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-600/90 text-white shadow-sm transition hover:bg-red-500"
                                    >
                                        ×
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
                        placeholder-title="{{ __('Drag in lifestyle or detail shots') }}"
                        placeholder-description="{{ __('Drop multiple files at once to build rich galleries in seconds.') }}"
                        button-text="{{ __('Add images') }}"
                        helper-text="{{ __('PNG, JPG or WebP - Up to 8 images per upload') }}"
                        icon-classes="bg-purple-100 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400"
                        highlight-class="border-purple-500/80 bg-purple-50/80 dark:border-purple-400/60 dark:bg-purple-900/20"
                        hover-class="hover:border-purple-500 hover:bg-purple-50/80 dark:hover:border-purple-400/60 dark:hover:bg-purple-900/30"
                        empty-hint="{{ __('Drag and drop product angles to craft a compelling story.') }}"
                        loading-text="{{ __('Uploading gallery images...') }}"
                        loading-class="text-purple-600 dark:text-purple-400"
                    />

                    <flux:error name="gallery_images.*" />
                </flux:field>
            </div>
        </x-products.form-section>

        <x-products.form-section 
            title="{{ __('Publishing Controls') }}"
            description="{{ __('Toggle storefront visibility and mark featured placements.') }}"
        >

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                    <flux:checkbox wire:model="is_active" label="{{ __('Active') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Inactive products remain hidden from the storefront but stay editable.') }}</p>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                    <flux:checkbox wire:model="is_featured" label="{{ __('Featured') }}" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Feature flagship items on the landing page or curated collections.') }}</p>
                </div>
            </div>
        </x-products.form-section>

        <div class="flex flex-wrap items-center gap-3 border-t border-zinc-200 dark:border-zinc-700 pt-6">
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
