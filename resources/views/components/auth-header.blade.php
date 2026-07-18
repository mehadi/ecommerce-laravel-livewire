@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5 text-center">
    <h1 class="font-display text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
    <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
</div>
