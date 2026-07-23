{{--
    Shared product image gallery: a main image plus clickable thumbnails
    that swap it, via a small Alpine `active` index — no Livewire round-trip.
    Reused by every product-details variant.

    Required: $product.
    Optional: $thumbLayout = 'row' (default, thumbnails below) | 'rail'
    (vertical thumbnails beside the main image) | 'none' (no thumbnails).
    Optional: $zoom (bool) — stronger hover-zoom on the main image.
    Optional: $mainAspect (Tailwind aspect class, default 'aspect-square').
--}}
@php
    $thumbLayout = $thumbLayout ?? 'row';
    $zoom = $zoom ?? false;
    $mainAspect = $mainAspect ?? 'aspect-square';

    $images = array_values(array_filter(array_merge(
        [$product->primary_image],
        $product->gallery_images ?? []
    )));
    $imageUrls = collect($images)->map(fn ($image) => asset('storage/'.$image))->all();

    if (empty($imageUrls)) {
        // Self-hosted placeholder (matches the icon used for image-less products
        // in product-card.blade.php) instead of the deprecated via.placeholder.com.
        $placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="3" fill="#f4f4f5"/><path stroke="#a1a1aa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
        $imageUrls = ['data:image/svg+xml,'.rawurlencode($placeholderSvg)];
    }
@endphp

<div x-data="{ images: @js($imageUrls), active: 0, name: @js($product->name), total: {{ count($imageUrls) }}, zoomed: false }" class="{{ $thumbLayout === 'rail' ? 'flex gap-4' : '' }}">
    @if($thumbLayout === 'rail' && count($imageUrls) > 1)
        <div class="flex flex-col gap-3 flex-shrink-0">
            <template x-for="(img, index) in images" :key="index">
                <button
                    type="button"
                    @click="active = index; zoomed = false"
                    :aria-current="active === index ? 'true' : 'false'"
                    :aria-label="`{{ __('View image') }} ${index + 1} {{ __('of') }} ${total}`"
                    :class="active === index ? 'ring-2 ring-emerald-500 dark:ring-emerald-400' : 'ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:ring-emerald-400/60'"
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden transition-all duration-200 cursor-pointer touch-manipulation flex-shrink-0"
                >
                    <img :src="img" :alt="`${name} - {{ __('thumbnail') }} ${index + 1}`" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    @endif

    <div class="flex-1">
        <div
            class="relative group overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-50 to-emerald-50/40 dark:from-zinc-800 dark:to-zinc-800/60 ring-1 ring-zinc-900/[0.05] dark:ring-white/[0.06] p-6 sm:p-10"
            @click="if (window.matchMedia('(pointer: coarse)').matches) zoomed = !zoomed"
        >
            <div class="pointer-events-none absolute -top-16 -right-16 w-56 h-56 bg-emerald-200/30 dark:bg-emerald-500/[0.06] rounded-full blur-3xl"></div>
            <img
                :src="images[active]"
                :alt="total > 1 ? `${name} - {{ __('image') }} ${active + 1} {{ __('of') }} ${total}` : name"
                width="600"
                height="600"
                :class="zoomed ? 'scale-125' : ''"
                class="relative w-full {{ $mainAspect }} object-contain drop-shadow-xl transition-transform duration-500 motion-reduce:transform-none {{ $zoom ? 'group-hover:scale-110' : 'transform group-hover:scale-[1.03]' }}"
            >
        </div>

        @if($thumbLayout === 'row' && count($imageUrls) > 1)
            <div class="grid grid-cols-4 gap-3 mt-4">
                <template x-for="(img, index) in images" :key="index">
                    <button
                        type="button"
                        @click="active = index; zoomed = false"
                        :aria-current="active === index ? 'true' : 'false'"
                        :aria-label="`{{ __('View image') }} ${index + 1} {{ __('of') }} ${total}`"
                        :class="active === index ? 'ring-2 ring-emerald-500 dark:ring-emerald-400' : 'ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] hover:ring-2 hover:ring-emerald-500 dark:hover:ring-emerald-400 hover:opacity-90'"
                        class="aspect-square rounded-2xl overflow-hidden transition-all duration-200 cursor-pointer touch-manipulation"
                    >
                        <img :src="img" :alt="`${name} - {{ __('thumbnail') }} ${index + 1}`" loading="lazy" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        @endif
    </div>
</div>
