<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Verify your email')" :description="__('Please verify your email address by clicking on the link we just emailed to you.')" />

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700 ring-1 ring-emerald-600/10 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-500/20">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" color="emerald" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
               <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth>
