@php
    $siteName = config('app.name');
    $siteUrl = config('app.url');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Primary Meta Tags -->
    <title>{{ $title ?? $siteName }}</title>
    @if($metaDescription ?? null)
        <meta name="description" content="{{ $metaDescription }}" />
    @endif
    <meta name="robots" content="index, follow" />

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $title ?? $siteName }}" />
    @if($metaDescription ?? null)
        <meta property="og:description" content="{{ $metaDescription }}" />
    @endif
    <meta property="og:site_name" content="{{ $siteName }}" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title ?? $siteName }}" />
    @if($metaDescription ?? null)
        <meta name="twitter:description" content="{{ $metaDescription }}" />
    @endif

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $siteUrl }}{{ request()->getRequestUri() }}" />

    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    @stack('head')
</head>
<body class="min-h-screen bg-zinc-100 dark:bg-zinc-950">
    <x-platform.navbar />

    <main>
        {{ $slot }}
    </main>

    <x-platform.footer />

    @stack('scripts')
    @fluxScripts
</body>
</html>
