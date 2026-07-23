<div
    class="min-h-screen bg-zinc-100 dark:bg-zinc-950"
    x-data
    @open-cart.window="$wire.set('showCart', true)"
>
    <!-- Order Confirmation Modal -->
    @include('components.public.order-success-modal', [
        'showOrderConfirmation' => $this->showOrderConfirmation,
        'order' => $this->order
    ])

    <!-- Hero Section -->
    <x-public.hero-section
        :heroSection="$this->heroSection"
        :product="$this->heroProduct"
        :heroSpotlightProduct="$this->heroSpotlightProduct"
        :socialFacebook="$this->socialFacebook"
        :socialInstagram="$this->socialInstagram"
        :socialTwitter="$this->socialTwitter"
        :defaultPrimaryCtaLabel="__('Shop Now')"
        defaultPrimaryCtaUrl="/shop"
        :defaultSecondaryCtaLabel="__('Browse Categories')"
        defaultSecondaryCtaUrl="/categories"
    />

    <!-- Trust Badges Section -->
    <x-public.trust-badges />

    <!-- Featured Categories Section -->
    <x-public.featured-categories-section :categories="$this->featuredCategories" />

    <!-- Featured Products Section -->
    <x-public.featured-products-section :products="$this->featuredProducts" />

    <!-- Testimonials Section -->
    <x-public.testimonials-section :testimonials="$this->testimonials" />

    <!-- FAQ Section -->
    <x-public.faq-section :faqs="$this->faqs" />

    <!-- Shop CTA Section -->
    <x-public.shop-cta-section />

    <!-- Sticky Cart Button -->
    <x-public.sticky-cart-button :cart="$this->cart" :cartFinalTotal="$this->cartFinalTotal" />

    <!-- Cart Modal -->
    @include('components.public.cart-modal')

    <!-- Checkout Modal -->
    @include('components.public.checkout-modal', [
        'showCheckout' => $this->showCheckout,
        'cart' => $this->cart,
        'cartFinalTotal' => $this->cartFinalTotal
    ])
</div>
