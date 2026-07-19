@php
    use App\Models\Setting;
    $siteName = Setting::get('site_name', config('app.name'));
    $siteTagline = Setting::get('site_tagline', '');
    $siteDescription = Setting::get('site_description', '');
    $metaDescription = $metaDescription ?? Setting::get('meta_description', $siteDescription);
    $metaKeywords = Setting::get('meta_keywords', '');
    $siteFavicon = Setting::get('site_favicon');
    $siteOgImage = Setting::get('site_og_image');
    // Pages with their own social image (a product, a landing page) pass $ogImage;
    // otherwise fall back to the tenant's default social image.
    $ogImageUrl = isset($ogImage) && $ogImage ? asset('storage/'.$ogImage) : ($siteOgImage ? asset('storage/'.$siteOgImage) : null);
    $ogType = $ogType ?? 'website';
    $googleVerificationCode = Setting::get('google_verification_code');
    $bingVerificationCode = Setting::get('bing_verification_code');
    $googleAnalyticsId = Setting::get('google_analytics_id');
    $googleTagManagerId = Setting::get('google_tag_manager_id');
    $facebookPixelId = Setting::get('facebook_pixel_id');
    $customHeaderCode = Setting::get('custom_header_code');
    $customFooterCode = Setting::get('custom_footer_code');

    $textSizePresets = ['xs' => 80, 'sm' => 90, 'medium' => 100, 'lg' => 112.5, 'xl' => 125, 'xxl' => 137.5];
    $textSize = Setting::get('frontend_text_size', 'medium');
    if ($textSize === 'custom') {
        $textSizePercent = max(50, min(200, (int) Setting::get('frontend_text_size_custom', 100)));
    } else {
        $textSizePercent = $textSizePresets[$textSize] ?? 100;
    }

    $contentWidthPresets = ['narrow' => 960, 'medium' => 1152, 'wide' => 1280, 'xl' => 1440, 'xxl' => 1600, 'full' => 1920];
    $contentWidth = Setting::get('frontend_content_width', 'medium');
    if ($contentWidth === 'custom') {
        $contentWidthPx = max(960, min(1920, (int) Setting::get('frontend_content_width_custom', 1152)));
    } else {
        $contentWidthPx = $contentWidthPresets[$contentWidth] ?? 1152;
    }

    $themePrimaryColor = Setting::get('theme_primary_color', '#059669');
    $themeSecondaryColor = Setting::get('theme_secondary_color', '#0f172a');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" style="font-size: {{ $textSizePercent }}%; --content-max-width: {{ $contentWidthPx }}px; --tenant-primary: {{ $themePrimaryColor }}; --tenant-secondary: {{ $themeSecondaryColor }};">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Font preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tenant brand color (set in Admin > Website Settings > Appearance) -->
    <style>
        .btn-tenant-primary { background-color: var(--tenant-primary); }
        .btn-tenant-primary:hover { filter: brightness(0.92); }
    </style>


    <!-- Primary Meta Tags -->
    <title>{{ $title ?? $siteName }}</title>
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}" />
    @endif
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}" />
    @endif
    <meta name="robots" content="index, follow" />
    
    <!-- Favicon -->
    @if($siteFavicon)
        <link rel="icon" href="{{ asset('storage/'.$siteFavicon) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/'.$siteFavicon) }}" type="image/x-icon">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $title ?? $siteName }}" />
    @if($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}" />
    @endif
    @if($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}" />
    @endif
    <meta property="og:site_name" content="{{ $siteName }}" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title ?? $siteName }}" />
    @if($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}" />
    @endif
    @if($ogImageUrl)
        <meta name="twitter:image" content="{{ $ogImageUrl }}" />
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}" />
    
    <!-- Site Verification -->
    @if($googleVerificationCode)
        <meta name="google-site-verification" content="{{ $googleVerificationCode }}" />
    @endif
    @if($bingVerificationCode)
        <meta name="msvalidate.01" content="{{ $bingVerificationCode }}" />
    @endif
    
    @stack('meta')
    
    <!-- Google Tag Manager -->
    @if($googleTagManagerId)
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $googleTagManagerId }}');</script>
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')

    {{-- Custom Header Code (Admin > Website Settings > Custom Code) --}}
    @if($customHeaderCode)
        {!! $customHeaderCode !!}
    @endif
</head>
<body class="min-h-screen bg-zinc-100 dark:bg-zinc-950">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2 focus:rounded-full focus:bg-emerald-600 focus:text-white focus:text-sm focus:font-semibold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
        {{ __('Skip to content') }}
    </a>

    <!-- Google Tag Manager (noscript) -->
    @if($googleTagManagerId)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $googleTagManagerId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @if($showNavigation ?? true)
        <x-public.navigation />
    @endif

    <main id="main-content">
        {{ $slot }}
    </main>

    @if($showFooter ?? true)
        <x-public.footer />
    @endif

    @if($showCookieConsent ?? true)
        <x-public.cookie-consent />
    @endif

    <!-- Google Analytics -->
    @if($googleAnalyticsId)
        @if(str_starts_with($googleAnalyticsId, 'G-'))
            <!-- Google Analytics 4 -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $googleAnalyticsId }}');
            </script>
        @elseif(str_starts_with($googleAnalyticsId, 'UA-'))
            <!-- Universal Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $googleAnalyticsId }}');
            </script>
        @endif
    @endif

    <!-- Facebook Pixel -->
    @if($facebookPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $facebookPixelId }}');
        fbq('track', 'PageView');

        // Cart/checkout components dispatch fbq:track(eventName, data) via
        // $this->dispatch() (see HasShoppingCart, LandingPage) for AddToCart,
        // InitiateCheckout, and Purchase — relay those into the pixel here.
        document.addEventListener('livewire:init', () => {
            Livewire.on('fbq:track', (params) => {
                const [eventName, data] = params;
                fbq('track', eventName, data || {});
            });
        });
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1"
    /></noscript>
    @endif

    @stack('scripts')

    {{-- Custom Footer Code (Admin > Website Settings > Custom Code) --}}
    @if($customFooterCode)
        {!! $customFooterCode !!}
    @endif

    @fluxScripts
</body>
</html>
