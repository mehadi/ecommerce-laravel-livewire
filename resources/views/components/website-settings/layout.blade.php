<div class="flex items-start max-md:flex-col gap-8">
    <div class="w-full md:w-[240px] shrink-0 md:sticky md:top-6 md:self-start">
        <div class="hidden md:block">
            <flux:navlist>
                <flux:navlist.item icon="globe-alt" :href="route('admin.website.index')" :current="request()->routeIs('admin.website.index')" wire:navigate>{{ __('General') }}</flux:navlist.item>
                <flux:navlist.item icon="language" :href="route('admin.website.appearance')" :current="request()->routeIs('admin.website.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
                <flux:navlist.item icon="sparkles" :href="route('admin.website.hero')" :current="request()->routeIs('admin.website.hero')" wire:navigate>{{ __('Hero Section') }}</flux:navlist.item>
                <flux:navlist.item icon="bars-3" :href="route('admin.website.header')" :current="request()->routeIs('admin.website.header')" wire:navigate>{{ __('Header') }}</flux:navlist.item>
                <flux:navlist.item icon="squares-2x2" :href="route('admin.website.product-grid')" :current="request()->routeIs('admin.website.product-grid')" wire:navigate>{{ __('Product Grid') }}</flux:navlist.item>
                <flux:navlist.item icon="photo" :href="route('admin.website.product-details')" :current="request()->routeIs('admin.website.product-details')" wire:navigate>{{ __('Product Details Page') }}</flux:navlist.item>
                <flux:navlist.item icon="rectangle-group" :href="route('admin.website.category-grid')" :current="request()->routeIs('admin.website.category-grid')" wire:navigate>{{ __('Category Grid') }}</flux:navlist.item>
                <flux:navlist.item icon="bars-3-bottom-left" :href="route('admin.website.footer')" :current="request()->routeIs('admin.website.footer')" wire:navigate>{{ __('Footer') }}</flux:navlist.item>
                <flux:navlist.item icon="envelope" :href="route('admin.website.contact')" :current="request()->routeIs('admin.website.contact')" wire:navigate>{{ __('Contact Information') }}</flux:navlist.item>
                <flux:navlist.item icon="share" :href="route('admin.website.social')" :current="request()->routeIs('admin.website.social')" wire:navigate>{{ __('Social Media') }}</flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('admin.website.analytics')" :current="request()->routeIs('admin.website.analytics')" wire:navigate>{{ __('Analytics & Tracking') }}</flux:navlist.item>
                <flux:navlist.item icon="shield-check" :href="route('admin.website.verification')" :current="request()->routeIs('admin.website.verification')" wire:navigate>{{ __('Site Verification') }}</flux:navlist.item>
                <flux:navlist.item icon="magnifying-glass" :href="route('admin.website.seo')" :current="request()->routeIs('admin.website.seo')" wire:navigate>{{ __('SEO Settings') }}</flux:navlist.item>
                <flux:navlist.item icon="globe-alt" :href="route('admin.website.domains')" :current="request()->routeIs('admin.website.domains')" wire:navigate>{{ __('Custom Domains') }}</flux:navlist.item>
                <flux:navlist.item icon="banknotes" :href="route('admin.website.localization')" :current="request()->routeIs('admin.website.localization')" wire:navigate>{{ __('Localization') }}</flux:navlist.item>
                @if(auth()->user()->hasRole('super admin'))
                    <flux:navlist.item icon="code-bracket" :href="route('admin.website.custom-code')" :current="request()->routeIs('admin.website.custom-code')" wire:navigate>{{ __('Custom Code') }}</flux:navlist.item>
                @endif
            </flux:navlist>
        </div>

        <div class="flex md:hidden gap-2 overflow-x-auto pb-2 -mx-1 px-1">
            <flux:button size="sm" :href="route('admin.website.index')" :variant="request()->routeIs('admin.website.index') ? 'primary' : 'ghost'" wire:navigate>{{ __('General') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.appearance')" :variant="request()->routeIs('admin.website.appearance') ? 'primary' : 'ghost'" wire:navigate>{{ __('Appearance') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.hero')" :variant="request()->routeIs('admin.website.hero') ? 'primary' : 'ghost'" wire:navigate>{{ __('Hero Section') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.header')" :variant="request()->routeIs('admin.website.header') ? 'primary' : 'ghost'" wire:navigate>{{ __('Header') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.product-grid')" :variant="request()->routeIs('admin.website.product-grid') ? 'primary' : 'ghost'" wire:navigate>{{ __('Product Grid') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.product-details')" :variant="request()->routeIs('admin.website.product-details') ? 'primary' : 'ghost'" wire:navigate>{{ __('Product Details Page') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.category-grid')" :variant="request()->routeIs('admin.website.category-grid') ? 'primary' : 'ghost'" wire:navigate>{{ __('Category Grid') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.footer')" :variant="request()->routeIs('admin.website.footer') ? 'primary' : 'ghost'" wire:navigate>{{ __('Footer') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.contact')" :variant="request()->routeIs('admin.website.contact') ? 'primary' : 'ghost'" wire:navigate>{{ __('Contact Information') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.social')" :variant="request()->routeIs('admin.website.social') ? 'primary' : 'ghost'" wire:navigate>{{ __('Social Media') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.analytics')" :variant="request()->routeIs('admin.website.analytics') ? 'primary' : 'ghost'" wire:navigate>{{ __('Analytics & Tracking') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.verification')" :variant="request()->routeIs('admin.website.verification') ? 'primary' : 'ghost'" wire:navigate>{{ __('Site Verification') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.seo')" :variant="request()->routeIs('admin.website.seo') ? 'primary' : 'ghost'" wire:navigate>{{ __('SEO Settings') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.domains')" :variant="request()->routeIs('admin.website.domains') ? 'primary' : 'ghost'" wire:navigate>{{ __('Custom Domains') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.localization')" :variant="request()->routeIs('admin.website.localization') ? 'primary' : 'ghost'" wire:navigate>{{ __('Localization') }}</flux:button>
            @if(auth()->user()->hasRole('super admin'))
                <flux:button size="sm" :href="route('admin.website.custom-code')" :variant="request()->routeIs('admin.website.custom-code') ? 'primary' : 'ghost'" wire:navigate>{{ __('Custom Code') }}</flux:button>
            @endif
        </div>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 min-w-0">
        <div class="mb-6">
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $subheading ?? '' }}</flux:text>
        </div>

        @if (session()->has('message'))
            <flux:callout variant="success" class="mb-6">{{ session('message') }}</flux:callout>
        @endif

        @if (session()->has('error'))
            <flux:callout variant="danger" class="mb-6">{{ session('error') }}</flux:callout>
        @endif

        {{ $slot }}
    </div>
</div>
