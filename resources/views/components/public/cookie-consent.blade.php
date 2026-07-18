<div
    x-data="{ show: !localStorage.getItem('cookie-consent') }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-4 left-4 right-4 sm:left-6 sm:right-auto sm:max-w-md z-50"
    role="dialog"
    aria-label="{{ __('Cookie consent') }}"
    style="display: none;"
>
    <div class="bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-3xl p-6 shadow-[0_24px_64px_-16px_rgb(16_24_40_/_0.25)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06]">
        <div class="flex items-start gap-3.5">
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 ring-1 ring-emerald-600/10 dark:ring-emerald-500/20 flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ __('We use cookies to enhance your browsing experience and provide personalized content') }}</p>
                <button @click="localStorage.setItem('cookie-consent', 'true'); show = false" class="mt-3.5 w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-200 shadow-md shadow-emerald-600/20 hover:shadow-lg cursor-pointer touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-900">
                    {{ __('Accept All Cookies') }}
                </button>
            </div>
        </div>
    </div>
</div>
