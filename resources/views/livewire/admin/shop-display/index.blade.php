<div class="space-y-6">
    <x-admin.page-header :heading="__('Shop Page Display')" :description="__('Control the grid layout and pagination options shown on the public /shop page and category product listings')" />

    @if (session()->has('message'))
        <flux:callout variant="success" wire:key="success-message-{{ time() }}">{{ session('message') }}</flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Grid Columns') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Choose how many columns shoppers can switch between on the product grid') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Column Options') }}</flux:label>
                <flux:input wire:model="columns_options" placeholder="2, 3, 4" />
                <flux:description>{{ __('Comma-separated whole numbers (2–4) shoppers can choose from.') }}</flux:description>
                <flux:error name="columns_options" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Default Columns') }}</flux:label>
                <flux:input type="number" min="2" max="4" wire:model="columns_default" />
                <flux:description>{{ __('Must be one of the column options above.') }}</flux:description>
                <flux:error name="columns_default" />
            </flux:field>
        </div>

        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Pagination Options') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Choose how many products are shown per page') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Per-Page Options') }}</flux:label>
                <flux:input wire:model="per_page_options" placeholder="6, 12, 24, 48" />
                <flux:description>{{ __('Comma-separated whole numbers (1–200) shoppers can choose from.') }}</flux:description>
                <flux:error name="per_page_options" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Default Per Page') }}</flux:label>
                <flux:input type="number" min="1" max="200" wire:model="per_page_default" />
                <flux:description>{{ __('Must be one of the per-page options above.') }}</flux:description>
                <flux:error name="per_page_default" />
            </flux:field>
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
                <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
