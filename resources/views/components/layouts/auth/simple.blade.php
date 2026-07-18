@php
    use App\Models\Setting;
    $siteName = Setting::get('site_name', config('app.name'));
    $siteLogo = Setting::get('site_logo');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-100 antialiased dark:bg-zinc-950">
        <div class="relative flex min-h-svh flex-col items-center justify-center gap-8 overflow-hidden p-6 md:p-10">
            {{-- Ambient glows, echoing the storefront hero --}}
            <div class="pointer-events-none absolute -top-24 -left-24 h-96 w-96 rounded-full bg-white/60 blur-3xl dark:bg-white/[0.03]" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-24 -bottom-32 h-[500px] w-[500px] rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-500/[0.05]" aria-hidden="true"></div>

            <div class="relative flex w-full max-w-md flex-col items-center gap-6">
                <a href="{{ route('home') }}" class="group flex items-center gap-2.5" wire:navigate>
                    @if($siteLogo)
                        <img src="{{ asset('storage/'.$siteLogo) }}" alt="{{ $siteName }}" class="h-9 w-auto rounded-xl transition-transform duration-300 group-hover:scale-[1.04] motion-reduce:transform-none">
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-[0.65rem] bg-zinc-900 shadow-sm transition-transform duration-300 group-hover:scale-[1.04] motion-reduce:transform-none dark:bg-white">
                            <span class="font-display text-lg leading-none font-extrabold text-white dark:text-zinc-900">{{ substr($siteName, 0, 1) }}</span>
                        </span>
                    @endif
                    <span class="font-display text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $siteName }}<span aria-hidden="true">.</span></span>
                </a>

                <div class="w-full rounded-[2rem] bg-white p-8 shadow-[0_1px_3px_rgb(16_24_40_/_0.03),0_12px_32px_-16px_rgb(16_24_40_/_0.08)] ring-1 ring-zinc-900/[0.04] sm:p-10 dark:bg-zinc-900 dark:ring-white/[0.06]">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
