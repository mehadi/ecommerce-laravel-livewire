@props(['colspan', 'title', 'description' => null])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center gap-3">
            <span class="h-12 w-12 text-zinc-400">
                @isset($icon)
                    {{ $icon }}
                @else
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0l-2 7H6l-2-7m16 0H4" />
                    </svg>
                @endisset
            </span>
            <div class="text-center">
                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $title }}</p>
                @if($description)
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $description }}
                    </p>
                @endif
            </div>
            {{ $slot }}
        </div>
    </td>
</tr>
