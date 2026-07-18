<div class="flex items-start max-md:flex-col gap-8">
    <div class="w-full md:w-[240px] shrink-0 md:sticky md:top-6 md:self-start">
        <div class="hidden md:block">
            <flux:navlist>
                <flux:navlist.item icon="globe-alt" :href="route('admin.website.index')" :current="request()->routeIs('admin.website.index')" wire:navigate>{{ __('General') }}</flux:navlist.item>
                <flux:navlist.item icon="language" :href="route('admin.website.appearance')" :current="request()->routeIs('admin.website.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
                <flux:navlist.item icon="envelope" :href="route('admin.website.contact')" :current="request()->routeIs('admin.website.contact')" wire:navigate>{{ __('Contact Information') }}</flux:navlist.item>
                <flux:navlist.item icon="share" :href="route('admin.website.social')" :current="request()->routeIs('admin.website.social')" wire:navigate>{{ __('Social Media') }}</flux:navlist.item>
                <flux:navlist.item icon="chart-bar" :href="route('admin.website.analytics')" :current="request()->routeIs('admin.website.analytics')" wire:navigate>{{ __('Analytics & Tracking') }}</flux:navlist.item>
                <flux:navlist.item icon="shield-check" :href="route('admin.website.verification')" :current="request()->routeIs('admin.website.verification')" wire:navigate>{{ __('Site Verification') }}</flux:navlist.item>
                <flux:navlist.item icon="magnifying-glass" :href="route('admin.website.seo')" :current="request()->routeIs('admin.website.seo')" wire:navigate>{{ __('SEO Settings') }}</flux:navlist.item>
            </flux:navlist>
        </div>

        <div class="flex md:hidden gap-2 overflow-x-auto pb-2 -mx-1 px-1">
            <flux:button size="sm" :href="route('admin.website.index')" :variant="request()->routeIs('admin.website.index') ? 'primary' : 'ghost'" wire:navigate>{{ __('General') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.appearance')" :variant="request()->routeIs('admin.website.appearance') ? 'primary' : 'ghost'" wire:navigate>{{ __('Appearance') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.contact')" :variant="request()->routeIs('admin.website.contact') ? 'primary' : 'ghost'" wire:navigate>{{ __('Contact Information') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.social')" :variant="request()->routeIs('admin.website.social') ? 'primary' : 'ghost'" wire:navigate>{{ __('Social Media') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.analytics')" :variant="request()->routeIs('admin.website.analytics') ? 'primary' : 'ghost'" wire:navigate>{{ __('Analytics & Tracking') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.verification')" :variant="request()->routeIs('admin.website.verification') ? 'primary' : 'ghost'" wire:navigate>{{ __('Site Verification') }}</flux:button>
            <flux:button size="sm" :href="route('admin.website.seo')" :variant="request()->routeIs('admin.website.seo') ? 'primary' : 'ghost'" wire:navigate>{{ __('SEO Settings') }}</flux:button>
        </div>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 min-w-0">
        <div class="mb-6">
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:text class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $subheading ?? '' }}</flux:text>
        </div>

        @if (session()->has('message'))
            <flux:callout variant="success" class="mb-6">{{ session('message') }}</flux:callout>
        @endif

        {{ $slot }}
    </div>
</div>
