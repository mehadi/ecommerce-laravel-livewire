<div class="space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <flux:heading>{{ __('Categories Page Display') }}</flux:heading>
            <flux:text size="sm" variant="subtle" class="mt-1">
                {{ __('Control the grid layout and pagination options shown on the public /categories page') }}
            </flux:text>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success" wire:key="success-message-{{ time() }}">{{ session('message') }}</flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/20">
                    <flux:icon.view-columns class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Grid Columns') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Choose how many columns visitors can switch between on the categories grid') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Column Options') }}</flux:label>
                <flux:input wire:model="columns_options" placeholder="4, 6, 8, 10" />
                <flux:description>{{ __('Comma-separated whole numbers (2–12) visitors can choose from.') }}</flux:description>
                <flux:error name="columns_options" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Default Columns') }}</flux:label>
                <flux:input type="number" min="2" max="12" wire:model="columns_default" />
                <flux:description>{{ __('Must be one of the column options above.') }}</flux:description>
                <flux:error name="columns_default" />
            </flux:field>
        </div>

        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                    <flux:icon.squares-2x2 class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Pagination Options') }}</flux:heading>
                    <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Choose how many categories are shown per page') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Per-Page Options') }}</flux:label>
                <flux:input wire:model="per_page_options" placeholder="12, 24, 48" />
                <flux:description>{{ __('Comma-separated whole numbers (1–200) visitors can choose from.') }}</flux:description>
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
            <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
        </div>
    </form>
</div>
