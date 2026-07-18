<section class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="space-y-1">
        <flux:heading size="sm">{{ $title }}</flux:heading>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ $description }}
        </p>
    </div>

    {{ $slot }}
</section>

