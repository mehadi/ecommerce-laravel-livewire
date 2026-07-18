@props(['product'])

@if($product)
    <section id="product" class="py-4 sm:py-5 scroll-mt-20">
        <div class="container mx-auto px-4 sm:px-6 frontend-container">
            <div class="bg-white dark:bg-zinc-900 rounded-[2rem] p-6 sm:p-10 lg:p-14 ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)]">
            <div class="grid md:grid-cols-2 gap-10 md:gap-14 lg:gap-16 items-start">
                <!-- Product Images -->
                <div class="md:sticky md:top-24">
                    <div class="relative group overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-50 to-emerald-50/40 dark:from-zinc-800 dark:to-zinc-800/60 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.06] p-6 sm:p-10">
                        <div class="pointer-events-none absolute -top-16 -right-16 w-56 h-56 bg-emerald-200/30 dark:bg-emerald-500/[0.06] rounded-full blur-3xl"></div>
                        <img src="{{ $product->primary_image ? asset('storage/'.$product->primary_image) : 'https://via.placeholder.com/600' }}" alt="{{ $product->name }}" width="600" height="600" class="relative w-full aspect-square object-contain drop-shadow-xl transform group-hover:scale-[1.03] transition-transform duration-500 motion-reduce:transform-none">
                    </div>
                    @if($product->gallery_images && count($product->gallery_images) > 0)
                        <div class="grid grid-cols-4 gap-3 mt-4">
                            @foreach($product->gallery_images as $image)
                                <img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name }}" loading="lazy" class="aspect-square object-cover rounded-2xl cursor-pointer ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:ring-2 hover:ring-emerald-500 dark:hover:ring-emerald-400 hover:opacity-90 transition-all duration-200">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="space-y-5">
                    <div>
                        <h2 class="font-display text-3xl sm:text-4xl lg:text-[2.75rem] font-bold text-zinc-900 dark:text-white mb-4 leading-[1.1] tracking-tight text-balance">{{ $product->name }}</h2>
                        @if(!$product->hasAttributes())
                            <div class="flex flex-wrap items-center gap-2.5">
                                @if($product->isInStock())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse motion-reduce:animate-none"></span>
                                        {{ __('In Stock') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-red-600/10 dark:ring-red-500/20">
                                        {{ __('Out of Stock') }}
                                    </span>
                                @endif
                                <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 tabular-nums">({{ $product->stock }} {{ __('available') }})</span>
                            </div>
                        @endif
                    </div>

                    {{-- New Attribute System --}}
                    @if($product->hasAttributes())
                        <!-- Attributes Selection -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8 space-y-5">
                            @php
                                // Get unique attribute names and their values from product attributes
                                $attributeData = [];
                                foreach ($product->productAttributes as $productAttribute) {
                                    foreach ($productAttribute->attribute_data as $key => $value) {
                                        if (!isset($attributeData[$key])) {
                                            $attributeData[$key] = [];
                                        }
                                        if (!in_array($value, $attributeData[$key])) {
                                            $attributeData[$key][] = $value;
                                        }
                                    }
                                }
                                $attributeNames = array_keys($attributeData);
                            @endphp

                            @foreach(\App\Models\Attribute::whereIn('name', $attributeNames)->where('is_active', true)->orderBy('order')->get() as $attribute)
                                @php
                                    // Get only the values that are actually used in this product's variations
                                    $usedValues = $attributeData[$attribute->name] ?? [];
                                    // Get attribute values that match the used values
                                    $availableValues = $attribute->activeValues->filter(function($value) use ($usedValues) {
                                        return in_array($value->value, $usedValues) || in_array($value->display_value, $usedValues);
                                    });
                                @endphp

                                @if($availableValues->isNotEmpty())
                                    <div>
                                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                                            {{ $attribute->name }}
                                            @if($attribute->unit)
                                                <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">({{ $attribute->unit }})</span>
                                            @endif
                                            @if($loop->first)
                                                <span class="text-red-500" aria-hidden="true">*</span>
                                            @endif
                                        </label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($availableValues as $value)
                                                @php
                                                    $isSelected = isset($this->selectedAttributeValues[$attribute->name]) && ($this->selectedAttributeValues[$attribute->name] === ($value->display_value ?: $value->value) || $this->selectedAttributeValues[$attribute->name] === $value->value);
                                                @endphp
                                                <button
                                                    type="button"
                                                    wire:click="selectAttributeValue('{{ $attribute->name }}', '{{ $value->display_value ?: $value->value }}')"
                                                    aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                    class="min-h-11 px-5 sm:px-6 py-2 rounded-full font-semibold text-sm transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800
                                                        @if($isSelected)
                                                            bg-emerald-600 text-white shadow-md shadow-emerald-600/20 ring-1 ring-emerald-600
                                                        @else
                                                            bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] hover:ring-emerald-500/60 dark:hover:ring-emerald-400/60 hover:text-emerald-700 dark:hover:text-emerald-300
                                                        @endif
                                                    "
                                                >
                                                    {{ $value->display_value ?: $value->value }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @php
                                $selectedProductAttribute = null;
                                if (!empty($this->selectedAttributeValues) && $this->selectedProductAttributeId) {
                                    $selectedProductAttribute = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
                                }
                            @endphp

                            @if($selectedProductAttribute)
                                <div class="pt-5 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
                                    <div class="flex flex-wrap items-end gap-x-4 gap-y-2">
                                        <div>
                                            <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mb-1 block">{{ __('Price') }}</span>
                                            <span class="font-display text-4xl sm:text-5xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight">৳{{ number_format($selectedProductAttribute->price, 2) }}</span>
                                        </div>
                                        @if($selectedProductAttribute->compare_at_price && $selectedProductAttribute->compare_at_price > $selectedProductAttribute->price)
                                            @php
                                                $discountPercent = round((($selectedProductAttribute->compare_at_price - $selectedProductAttribute->price) / $selectedProductAttribute->compare_at_price) * 100, 2);
                                            @endphp
                                            <div class="flex items-center gap-2 pb-1">
                                                <span class="text-lg sm:text-xl text-zinc-400 dark:text-zinc-500 line-through tabular-nums">৳{{ number_format($selectedProductAttribute->compare_at_price, 2) }}</span>
                                                <span class="inline-flex items-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
                                                    -{{ $discountPercent }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        @if($selectedProductAttribute->stock > 0)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse motion-reduce:animate-none"></span>
                                                {{ __('In Stock') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full text-xs sm:text-sm font-semibold ring-1 ring-red-600/10 dark:ring-red-500/20">
                                                {{ __('Out of Stock') }}
                                            </span>
                                        @endif
                                        <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 tabular-nums">({{ $selectedProductAttribute->stock }} {{ __('available') }})</span>
                                    </div>
                                </div>
                            @elseif(!empty($this->selectedAttributeValues))
                                <div class="pt-5 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
                                    <p class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        {{ __('Please select all attributes to see pricing') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Regular Price Display -->
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8">
                            <div class="flex flex-wrap items-end gap-x-4 gap-y-2">
                                <div>
                                    <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mb-1 block">{{ __('Price') }}</span>
                                    <span class="font-display text-4xl sm:text-5xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight">৳{{ number_format($product->price, 2) }}</span>
                                </div>
                                @if($product->hasDiscount())
                                    <div class="flex items-center gap-2 pb-1">
                                        <span class="text-lg sm:text-xl text-zinc-400 dark:text-zinc-500 line-through tabular-nums">৳{{ number_format($product->compare_at_price, 2) }}</span>
                                        <span class="inline-flex items-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-red-600/10 dark:ring-red-500/20 tabular-nums">
                                            -{{ $product->discountPercentage() }}%
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($product->description)
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8">
                            <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-3 flex items-center gap-2.5">
                                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                {{ __('About This Product') }}
                            </h3>
                            <p class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    @endif

                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8">
                        <h3 class="text-base sm:text-lg font-display font-semibold text-zinc-900 dark:text-white mb-4 flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 ring-1 ring-amber-600/10 dark:ring-amber-500/20">
                                <svg class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </span>
                            {{ __('Key Benefits') }}
                        </h3>
                        @if($product->benefits)
                            <div class="text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300 leading-relaxed space-y-2">
                                {!! nl2br(e($product->benefits)) !!}
                            </div>
                        @else
                            <ul class="space-y-3 text-sm sm:text-[15px] text-zinc-600 dark:text-zinc-300">
                                @foreach([__('100% Natural & Organic'), __('No Artificial Additives'), __('Rich in Nutrients & Vitamins')] as $benefit)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8">
                        <label for="product-quantity" class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">{{ __('Quantity') }}</label>
                        <div class="inline-flex items-center gap-1 bg-white dark:bg-zinc-900 rounded-full ring-1 ring-zinc-900/[0.08] dark:ring-white/[0.1] p-1">
                            <button wire:click="decrementQuantity" type="button" aria-label="{{ __('Decrease quantity') }}" class="w-11 h-11 rounded-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold text-lg transition-colors duration-200 flex items-center justify-center cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M20 12H4"></path></svg>
                            </button>
                            @php
                                $maxStock = $product->stock;
                                if ($product->hasAttributes() && $this->selectedProductAttributeId) {
                                    $attr = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
                                    $maxStock = $attr ? $attr->stock : $product->stock;
                                }
                            @endphp
                            <input id="product-quantity" type="number" inputmode="numeric" wire:model.live="quantity" min="1" max="{{ $maxStock }}" class="w-16 h-11 text-center bg-transparent border-0 font-semibold text-base text-zinc-900 dark:text-white tabular-nums focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button wire:click="incrementQuantity" type="button" aria-label="{{ __('Increase quantity') }}" class="w-11 h-11 rounded-full bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-bold text-lg transition-colors duration-200 flex items-center justify-center cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        @php
                            $isDisabled = false;
                            if ($product->hasAttributes()) {
                                $isDisabled = !$this->selectedProductAttributeId || empty($this->selectedAttributeValues);
                                if ($this->selectedProductAttributeId) {
                                    $attr = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
                                    $isDisabled = !$attr || $attr->stock <= 0 || !$attr->is_active;
                                }
                            } else {
                                $isDisabled = !$product->isInStock();
                            }
                        @endphp
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart" class="group w-full sm:flex-1 bg-white dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 px-8 py-4 sm:py-[18px] rounded-full text-base sm:text-lg font-bold transition-all duration-300 ring-1 ring-emerald-600/30 dark:ring-emerald-400/30 hover:ring-emerald-600 dark:hover:ring-emerald-400 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:translate-y-0 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900" {{ $isDisabled ? 'disabled' : '' }}>
                                <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <svg wire:loading wire:target="addToCart" class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                {{ __('Add to Cart') }}
                            </button>
                            <button wire:click="buyNow" wire:loading.attr="disabled" wire:target="buyNow" class="group w-full sm:flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 sm:py-[18px] rounded-full text-base sm:text-lg font-bold transition-all duration-300 shadow-md shadow-emerald-600/20 hover:shadow-lg hover:shadow-emerald-600/25 hover:-translate-y-0.5 motion-reduce:transform-none flex items-center justify-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900" {{ $isDisabled ? 'disabled' : '' }}>
                                <svg wire:loading.remove wire:target="buyNow" class="w-5 h-5 sm:w-6 sm:h-6 transition-transform duration-300 group-hover:translate-x-0.5 motion-reduce:transform-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <svg wire:loading wire:target="buyNow" class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                {{ __('Buy Now') }}
                            </button>
                        </div>
                        @if($product->hasAttributes() && empty($this->selectedAttributeValues))
                            <p class="mt-3 text-sm text-amber-700 dark:text-amber-400 text-center flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Please select all attributes to continue') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>
@endif

