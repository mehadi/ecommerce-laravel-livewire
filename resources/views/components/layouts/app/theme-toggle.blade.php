@props(['position' => 'right'])

<flux:tooltip :content="__('Toggle theme')" position="{{ $position }}">
    <flux:button x-data x-on:click="$flux.appearance = ($flux.appearance === 'dark' ? 'light' : 'dark')" variant="ghost" size="sm" square aria-label="{{ __('Toggle theme') }}">
        <flux:icon.sun class="dark:hidden" variant="mini" />
        <flux:icon.moon class="hidden dark:block" variant="mini" />
    </flux:button>
</flux:tooltip>
