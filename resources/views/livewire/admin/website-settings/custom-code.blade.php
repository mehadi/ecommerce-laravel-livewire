<x-website-settings.layout :heading="__('Custom Code')" :subheading="__('Inject custom HTML, CSS, or JavaScript into your storefront pages')">
    <form wire:submit="update" class="space-y-8">
        <flux:callout variant="warning" class="mb-2">
            {{ __('Code entered here runs on every storefront page, exactly as written, with no sanitization. Only paste code from sources you trust — invalid or malicious code can break your storefront, steal customer data, or hijack sessions. This is why only Super Admins can access this page; changes are logged for audit purposes.') }}
        </flux:callout>

        <!-- Header Code Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-sky-600 dark:text-sky-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5-3 4.5 3 4.5M17.25 7.5l3 4.5-3 4.5M14.25 3l-4.5 18" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Header Code') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Inserted right before the closing </head> tag on every page') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Custom Header Code') }}</flux:label>
                <flux:textarea
                    wire:model="custom_header_code"
                    rows="8"
                    maxlength="20000"
                    placeholder="<meta name=&quot;example&quot; content=&quot;value&quot; />"
                    class="font-mono text-sm"
                />
                <flux:description>{{ __('Meta tags, custom CSS <style> blocks, verification tags, or third-party <script> embeds. Max 20,000 characters.') }}</flux:description>
                <flux:error name="custom_header_code" />
            </flux:field>
        </div>

        <!-- Footer Code Card -->
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="md" level="3">{{ __('Footer Code') }}</flux:heading>
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Inserted right before the closing </body> tag on every page') }}</flux:text>
                </div>
            </div>

            <flux:field>
                <flux:label>{{ __('Custom Footer Code') }}</flux:label>
                <flux:textarea
                    wire:model="custom_footer_code"
                    rows="8"
                    maxlength="20000"
                    placeholder="<script>console.log('example');</script>"
                    class="font-mono text-sm"
                />
                <flux:description>{{ __('Chat widgets, extra tracking scripts, or any code best loaded last. Max 20,000 characters.') }}</flux:description>
                <flux:error name="custom_footer_code" />
            </flux:field>
        </div>

        <x-website-settings.save-bar action="update" />
    </form>
</x-website-settings.layout>
