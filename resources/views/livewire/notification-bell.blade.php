<div>
    <flux:dropdown position="bottom" align="end">
        <span class="relative inline-flex">
            <x-admin.icon-button aria-label="{{ __('Notifications') }}" variant="ghost" size="sm" square>
                <flux:icon.bell variant="mini" />
            </x-admin.icon-button>
            @if($unreadCount > 0)
                <span class="pointer-events-none absolute -top-1 -end-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold text-white">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </span>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between px-3 py-2">
                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Notifications') }}</span>
                @if($unreadCount > 0)
                    <flux:button wire:click="markAllAsRead" size="xs" variant="ghost">{{ __('Mark all as read') }}</flux:button>
                @endif
            </div>
            <flux:menu.separator />

            @forelse($notifications as $notification)
                <flux:menu.item
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="items-start {{ $notification->read_at ? '' : 'bg-blue-50/60 dark:bg-blue-900/10' }}"
                >
                    <div class="flex w-full items-start gap-2">
                        @unless($notification->read_at)
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-600" aria-hidden="true"></span>
                        @endunless
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $notification->data['title'] ?? '' }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $notification->data['body'] ?? '' }}</p>
                            <p class="mt-0.5 text-[11px] text-zinc-400 dark:text-zinc-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </flux:menu.item>
            @empty
                <div class="px-3 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No notifications yet.') }}
                </div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>
