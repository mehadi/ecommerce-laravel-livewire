@props([
    'field',
    'label',
    'sortField' => null,
    'sortDirection' => 'asc',
    'align' => 'left',
])

<th
    wire:click="sortBy('{{ $field }}')"
    class="cursor-pointer px-4 py-3 text-{{ $align }} text-xs font-medium uppercase tracking-wider text-zinc-500 transition-colors hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-700"
>
    <div class="flex items-center gap-2 {{ $align === 'right' ? 'justify-end' : '' }}">
        {{ $label }}
        @if($sortField === $field)
            <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        @endif
    </div>
</th>
