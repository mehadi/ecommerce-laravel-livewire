{{--
    Shared attribute/variant picker (size, color, etc.) plus the matched
    combination's price/discount/stock once every attribute is chosen.
    Reused by every product-details variant since it drives
    `selectAttributeValue()` on the enclosing Livewire component.

    Required: $product (must have hasAttributes() === true).
    Optional: $style = 'default' | 'compact' | 'plain' — controls card chrome.
--}}
@php
    $style = $style ?? 'default';
    $wrapClass = match ($style) {
        'plain' => 'space-y-4',
        'compact' => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-2xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-5 space-y-4',
        default => 'bg-zinc-50 dark:bg-zinc-800/60 rounded-3xl ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] p-6 sm:p-8 space-y-5',
    };
    $pillClass = $style === 'compact'
        ? 'min-h-9 px-4 py-1.5 text-xs'
        : 'min-h-11 px-5 sm:px-6 py-2 text-sm';

    $attributeData = [];
    foreach ($product->productAttributes as $productAttribute) {
        foreach ($productAttribute->attribute_data as $key => $value) {
            if (! isset($attributeData[$key])) {
                $attributeData[$key] = [];
            }
            if (! in_array($value, $attributeData[$key])) {
                $attributeData[$key][] = $value;
            }
        }
    }
    $attributeNames = array_keys($attributeData);
@endphp

<div class="{{ $wrapClass }}">
    @foreach(\App\Models\Attribute::whereIn('name', $attributeNames)->where('is_active', true)->orderBy('order')->get() as $attribute)
        @php
            $usedValues = $attributeData[$attribute->name] ?? [];
            $availableValues = $attribute->activeValues->filter(function ($value) use ($usedValues) {
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
                            class="{{ $pillClass }} rounded-full font-semibold transition-all duration-200 cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-800
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
        if (! empty($this->selectedAttributeValues) && $this->selectedProductAttributeId) {
            $selectedProductAttribute = \App\Models\ProductAttribute::find($this->selectedProductAttributeId);
        }
    @endphp

    @if($selectedProductAttribute)
        <div class="pt-5 border-t border-zinc-900/[0.06] dark:border-white/[0.08]">
            <div class="flex flex-wrap items-end gap-x-4 gap-y-2">
                <div>
                    <span class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mb-1 block">{{ __('Price') }}</span>
                    <span class="font-display text-4xl sm:text-5xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($selectedProductAttribute->price, 2) }}</span>
                </div>
                @if($selectedProductAttribute->compare_at_price && $selectedProductAttribute->compare_at_price > $selectedProductAttribute->price)
                    @php
                        $discountPercent = round((($selectedProductAttribute->compare_at_price - $selectedProductAttribute->price) / $selectedProductAttribute->compare_at_price) * 100, 2);
                    @endphp
                    <div class="flex items-center gap-2 pb-1">
                        <span class="text-lg sm:text-xl text-zinc-400 dark:text-zinc-500 line-through tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($selectedProductAttribute->compare_at_price, 2) }}</span>
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
    @elseif(! empty($this->selectedAttributeValues))
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
