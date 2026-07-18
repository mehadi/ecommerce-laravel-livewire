@php
    use App\Models\Setting;
    $siteName = Setting::get('site_name', config('app.name'));
    $siteTagline = Setting::get('site_tagline', '');
    $siteLogo = Setting::get('site_logo');
    $contactEmail = Setting::get('contact_email', '');
    $contactPhone = Setting::get('contact_phone', '');
    $contactAddress = Setting::get('contact_address', '');
    $socialFacebook = Setting::get('social_facebook', '');
    $socialInstagram = Setting::get('social_instagram', '');
    $socialTwitter = Setting::get('social_twitter', '');
    $socialLinkedIn = Setting::get('social_linkedin', '');
    $socialYouTube = Setting::get('social_youtube', '');
    $socialTikTok = Setting::get('social_tiktok', '');
    $socialPinterest = Setting::get('social_pinterest', '');
    $socialWhatsApp = Setting::get('social_whatsapp', '');

    $socialLinks = [
        ['url' => $socialFacebook, 'label' => 'Facebook', 'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
        ['url' => $socialInstagram, 'label' => 'Instagram', 'path' => 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.897 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.897-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z'],
        ['url' => $socialTwitter, 'label' => 'Twitter/X', 'path' => 'M18.9 2H22l-7.6 8.7L23 22h-6.9l-5.4-6.8L4.4 22H1.3l8.1-9.3L1 2h7.1l4.9 6.2L18.9 2zm-1.2 18h1.9L7.4 4H5.4l12.3 16z'],
        ['url' => $socialLinkedIn, 'label' => 'LinkedIn', 'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
        ['url' => $socialYouTube, 'label' => 'YouTube', 'path' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z'],
        ['url' => $socialTikTok, 'label' => 'TikTok', 'path' => 'M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z'],
        ['url' => $socialPinterest, 'label' => 'Pinterest', 'path' => 'M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z'],
    ];
    if ($socialWhatsApp) {
        $socialLinks[] = ['url' => 'https://wa.me/'.str_replace(['+', ' ', '-'], '', $socialWhatsApp), 'label' => 'WhatsApp', 'path' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z'];
    }
    $socialLinks = array_filter($socialLinks, fn ($link) => !empty($link['url']));
@endphp

<footer class="relative bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-t border-zinc-900/[0.04] dark:border-white/[0.06] pt-16 sm:pt-20 pb-10 overflow-hidden">
    {{-- Subtle brand glow --}}
    <div class="pointer-events-none absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-emerald-500/[0.05] rounded-full blur-3xl"></div>

    <div class="relative container mx-auto px-4 sm:px-6 frontend-container">
        <div class="grid gap-12 md:gap-8 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.2fr] mb-14">
            <div class="space-y-5">
                <a href="/" wire:navigate class="inline-flex items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-zinc-900">
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" loading="lazy" class="h-11 w-auto">
                    @else
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md shadow-emerald-600/20">
                            <span class="text-white font-display font-bold text-lg">{{ substr($siteName, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="font-display text-xl font-semibold tracking-tight">{{ $siteName }}</span>
                </a>
                @if($siteTagline)
                    <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed text-sm sm:text-[15px] max-w-xs">{{ $siteTagline }}</p>
                @endif
                @if(count($socialLinks) > 0)
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($socialLinks as $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-zinc-50 dark:bg-white/[0.06] ring-1 ring-zinc-900/[0.06] dark:ring-white/[0.08] hover:bg-emerald-50 dark:hover:bg-emerald-500/15 hover:ring-emerald-500/40 hover:text-emerald-600 dark:hover:text-emerald-400 text-zinc-500 dark:text-zinc-400 rounded-full flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 motion-reduce:transform-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" aria-label="{{ $link['label'] }}">
                                <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $link['path'] }}"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <nav aria-label="{{ __('Company') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Company') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    <li><a href="#features" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('About Us') }}</a></li>
                    <li><a href="#testimonials" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Testimonials') }}</a></li>
                    <li><a href="#faq" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('FAQ') }}</a></li>
                </ul>
            </nav>
            <nav aria-label="{{ __('Legal') }}">
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Legal') }}</h4>
                <ul class="space-y-3.5 text-[15px]">
                    <li><a href="#" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="#" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Terms of Service') }}</a></li>
                    <li><a href="#" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">{{ __('Refund Policy') }}</a></li>
                </ul>
            </nav>
            <div>
                <h4 class="text-sm font-semibold uppercase tracking-widest text-zinc-500 mb-5">{{ __('Contact Us') }}</h4>
                <ul class="space-y-4 text-[15px] text-zinc-500 dark:text-zinc-400">
                    @if($contactEmail)
                        <li>
                            <a href="mailto:{{ $contactEmail }}" class="flex items-start gap-3 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </span>
                                <span class="pt-2 break-all">{{ $contactEmail }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li>
                            <a href="tel:{{ $contactPhone }}" class="flex items-start gap-3 hover:text-zinc-900 dark:hover:text-white transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md">
                                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </span>
                                <span class="pt-2 tabular-nums">{{ $contactPhone }}</span>
                            </a>
                        </li>
                    @endif
                    @if($contactAddress)
                        <li class="flex items-start gap-3">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </span>
                            <span class="pt-2">{{ $contactAddress }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-zinc-900/[0.06] dark:border-white/[0.08] pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-zinc-500 dark:text-zinc-400 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
                </p>
                <div class="flex items-center gap-6 text-zinc-500 dark:text-zinc-400 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Secure Checkout') }}
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('SSL Encrypted') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>
