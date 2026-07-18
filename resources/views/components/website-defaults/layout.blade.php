<div class="flex items-start max-md:flex-col gap-8">
    <div class="w-full md:w-[240px] shrink-0 md:sticky md:top-6 md:self-start">
        <div class="hidden md:block">
            <flux:navlist>
                <flux:navlist.item icon="globe-alt" :href="route('platform.website-defaults.index')" :current="request()->routeIs('platform.website-defaults.index')" wire:navigate>{{ __('General') }}</flux:navlist.item>
                <flux:navlist.item icon="language" :href="route('platform.website-defaults.appearance')" :current="request()->routeIs('platform.website-defaults.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
                <flux:navlist.item icon="envelope" :href="route('platform.website-defaults.contact')" :current="request()->routeIs('platform.website-defaults.contact')" wire:navigate>{{ __('Contact Information') }}</flux:navlist.item>
                <flux:navlist.item icon="share" :href="route('platform.website-defaults.social')" :current="request()->routeIs('platform.website-defaults.social')" wire:navigate>{{ __('Social Media') }}</flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('platform.website-defaults.analytics')" :current="request()->routeIs('platform.website-defaults.analytics')" wire:navigate>{{ __('Analytics & Tracking') }}</flux:navlist.item>
                <flux:navlist.item icon="shield-check" :href="route('platform.website-defaults.verification')" :current="request()->routeIs('platform.website-defaults.verification')" wire:navigate>{{ __('Site Verification') }}</flux:navlist.item>
                <flux:navlist.item icon="magnifying-glass" :href="route('platform.website-defaults.seo')" :current="request()->routeIs('platform.website-defaults.seo')" wire:navigate>{{ __('SEO Settings') }}</flux:navlist.item>
            </flux:navlist>
        </div>

        <div class="flex md:hidden gap-2 overflow-x-auto pb-2 -mx-1 px-1">
            <flux:button size="sm" :href="route('platform.website-defaults.index')" :variant="request()->routeIs('platform.website-defaults.index') ? 'primary' : 'ghost'" wire:navigate>{{ __('General') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.appearance')" :variant="request()->routeIs('platform.website-defaults.appearance') ? 'primary' : 'ghost'" wire:navigate>{{ __('Appearance') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.contact')" :variant="request()->routeIs('platform.website-defaults.contact') ? 'primary' : 'ghost'" wire:navigate>{{ __('Contact Information') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.social')" :variant="request()->routeIs('platform.website-defaults.social') ? 'primary' : 'ghost'" wire:navigate>{{ __('Social Media') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.analytics')" :variant="request()->routeIs('platform.website-defaults.analytics') ? 'primary' : 'ghost'" wire:navigate>{{ __('Analytics & Tracking') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.verification')" :variant="request()->routeIs('platform.website-defaults.verification') ? 'primary' : 'ghost'" wire:navigate>{{ __('Site Verification') }}</flux:button>
            <flux:button size="sm" :href="route('platform.website-defaults.seo')" :variant="request()->routeIs('platform.website-defaults.seo') ? 'primary' : 'ghost'" wire:navigate>{{ __('SEO Settings') }}</flux:button>
        </div>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 min-w-0">
        <div class="mb-6">
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:text class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $subheading ?? '' }}</flux:text>
        </div>

        <flux:callout variant="info" class="mb-6">
            {{ __('These are platform-wide defaults. Any store that leaves a field blank in its own Website Settings will automatically use the value set here.') }}
        </flux:callout>

        @if (session()->has('message'))
            <flux:callout variant="success" class="mb-6">{{ session('message') }}</flux:callout>
        @endif

        {{ $slot }}
    </div>
</div>
