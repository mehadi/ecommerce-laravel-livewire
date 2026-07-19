{{-- Editorial product-details content — magazine-style full-width image, centered buy box, tabbed description/details. --}}
<div class="max-w-4xl mx-auto">
    @include('components.public.product-details._gallery', [
        'product' => $product,
        'thumbLayout' => 'row',
        'mainAspect' => 'aspect-[16/10]',
    ])

    <div class="mt-8 max-w-2xl mx-auto text-center">
        <{{ $asH1 ? 'h1' : 'h2' }} class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-zinc-900 dark:text-white mb-4 leading-[1.1] tracking-tight text-balance">{{ $product->name }}</{{ $asH1 ? 'h1' : 'h2' }}>
        @unless($product->hasAttributes())
            <div class="flex justify-center">
                @include('components.public.product-details._stock-badge', ['product' => $product])
            </div>
        @endunless
    </div>

    <div class="mt-6 max-w-md mx-auto space-y-5">
        @if($product->hasAttributes())
            @include('components.public.product-details._attribute-picker', ['product' => $product, 'style' => 'plain'])
        @else
            @include('components.public.product-details._simple-price', ['product' => $product, 'style' => 'plain'])
        @endif

        @include('components.public.product-details._quantity-stepper', ['product' => $product, 'style' => 'plain'])
        @include('components.public.product-details._actions', ['product' => $product, 'style' => 'default'])
    </div>

    <div class="mt-12" x-data="{ tab: 'description' }">
        <div class="flex items-center justify-center gap-2 border-b border-zinc-200 dark:border-zinc-800">
            <button type="button" @click="tab = 'description'" :class="tab === 'description' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors duration-200 cursor-pointer">
                {{ __('Description') }}
            </button>
            <button type="button" @click="tab = 'details'" :class="tab === 'details' ? 'border-emerald-600 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'" class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors duration-200 cursor-pointer">
                {{ __('Details') }}
            </button>
        </div>

        <div class="pt-6 max-w-2xl mx-auto">
            <div x-show="tab === 'description'">
                @include('components.public.product-details._description-benefits', ['product' => $product, 'style' => 'plain'])
            </div>
            <div x-show="tab === 'details'" style="display: none;">
                <dl class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                    @if($product->sku)
                        <div class="flex justify-between py-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('SKU') }}</dt>
                            <dd class="font-medium text-zinc-900 dark:text-white">{{ $product->sku }}</dd>
                        </div>
                    @endif
                    @if($product->category)
                        <div class="flex justify-between py-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Category') }}</dt>
                            <dd class="font-medium text-zinc-900 dark:text-white">{{ $product->category->name }}</dd>
                        </div>
                    @endif
                    @if($product->weight_kg)
                        <div class="flex justify-between py-3">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Weight') }}</dt>
                            <dd class="font-medium text-zinc-900 dark:text-white tabular-nums">{{ number_format($product->weight_kg, 2) }} kg</dd>
                        </div>
                    @endif
                    <div class="flex justify-between py-3">
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Availability') }}</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $product->isInStock() ? __('In Stock') : __('Out of Stock') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
