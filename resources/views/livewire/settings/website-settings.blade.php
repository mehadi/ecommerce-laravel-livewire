<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading>{{ __('Website Settings') }}</flux:heading>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage your website content and configuration') }}</p>
        </div>
    </div>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif
        <form wire:submit="update" class="mt-6 space-y-8">
            <!-- Site Information Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <flux:icon.globe-alt class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Site Information') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Basic information about your website') }}</flux:text>
                    </div>
                </div>

                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Site Name') }}</flux:label>
                        <flux:input
                            wire:model="site_name"
                            type="text"
                            placeholder="{{ config('app.name') }}"
                        />
                        <flux:description>{{ __('The name of your website displayed throughout the site') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Site Tagline') }}</flux:label>
                        <flux:input
                            wire:model="site_tagline"
                            type="text"
                            placeholder="{{ __('Premium Date Molasses - Natural Sweetener') }}"
                        />
                        <flux:description>{{ __('A short tagline that describes your website') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Site Description') }}</flux:label>
                        <flux:textarea
                            wire:model="site_description"
                            placeholder="{{ __('A brief description of your website') }}"
                            rows="3"
                        />
                        <flux:description>{{ __('A detailed description of your website and what you offer') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Site URL') }}</flux:label>
                        <flux:input
                            wire:model="site_url"
                            type="url"
                            placeholder="{{ config('app.url') }}"
                        />
                        <flux:description>{{ __('The canonical URL of your website (used for SEO and social sharing)') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Site Logo') }}</flux:label>
                        
                        <div
                            x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    e.preventDefault();
                                    this.isDragging = false;
                                    if (e.dataTransfer.files.length) {
                                        @this.upload('logo', e.dataTransfer.files[0]);
                                    }
                                },
                                handleDragOver(e) {
                                    e.preventDefault();
                                    this.isDragging = true;
                                },
                                handleDragLeave() {
                                    this.isDragging = false;
                                }
                            }"
                            @drop.prevent="handleDrop"
                            @dragover.prevent="handleDragOver"
                            @dragleave.prevent="handleDragLeave"
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-neutral-300 dark:border-neutral-600'"
                            class="relative border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                        >
                            @if($existing_logo || $logo)
                                <div class="space-y-4">
                                    @if($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="mx-auto max-h-32 object-contain rounded">
                                    @elseif($existing_logo)
                                        <img src="{{ asset('storage/'.$existing_logo) }}" alt="Current Logo" class="mx-auto max-h-32 object-contain rounded">
                                    @endif
                                    <div class="flex items-center justify-center gap-3">
                                        <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            {{ __('Change Logo') }}
                                            <input type="file" wire:model="logo" accept="image/*" class="hidden">
                                        </label>
                                        @if($existing_logo)
                                            <button type="button" wire:click="removeLogo" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                {{ __('Remove') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="space-y-4">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-2">
                                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop your logo here') }}</span>
                                            {{ __('or click to browse') }}
                                        </p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                            {{ __('PNG, JPG, GIF up to 2MB') }}
                                        </p>
                                    </div>
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __('Select Logo') }}
                                        <input type="file" wire:model="logo" accept="image/*" class="hidden">
                                    </label>
                                </div>
                            @endif
                        </div>
                        
                        <flux:description class="mt-2">
                            {{ __('Upload your website logo. If no logo is uploaded, the site name will be displayed as text.') }}
                        </flux:description>
                        <flux:error name="logo" />
                        <div wire:loading wire:target="logo" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                            {{ __('Uploading...') }}
                        </div>
                    </flux:field>

                    <!-- Favicon Upload -->
                    <flux:field>
                        <flux:label>{{ __('Favicon') }}</flux:label>
                        <div
                            x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    e.preventDefault();
                                    this.isDragging = false;
                                    if (e.dataTransfer.files.length) {
                                        @this.upload('favicon', e.dataTransfer.files[0]);
                                    }
                                },
                                handleDragOver(e) {
                                    e.preventDefault();
                                    this.isDragging = true;
                                },
                                handleDragLeave() {
                                    this.isDragging = false;
                                }
                            }"
                            @drop.prevent="handleDrop"
                            @dragover.prevent="handleDragOver"
                            @dragleave.prevent="handleDragLeave"
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-neutral-300 dark:border-neutral-600'"
                            class="relative border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                        >
                            @if($existing_favicon || $favicon)
                                <div class="space-y-3">
                                    @if($favicon)
                                        <img src="{{ $favicon->temporaryUrl() }}" alt="Favicon Preview" class="mx-auto max-h-16 object-contain rounded">
                                    @elseif($existing_favicon)
                                        <img src="{{ asset('storage/'.$existing_favicon) }}" alt="Current Favicon" class="mx-auto max-h-16 object-contain rounded">
                                    @endif
                                    <div class="flex items-center justify-center gap-3">
                                        <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            {{ __('Change') }}
                                            <input type="file" wire:model="favicon" accept="image/x-icon,image/png" class="hidden">
                                        </label>
                                        @if($existing_favicon)
                                            <button type="button" wire:click="removeFavicon" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                {{ __('Remove') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-neutral-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                        </svg>
                                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">
                                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop favicon here') }}</span>
                                            {{ __('or click to browse') }}
                                        </p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                            {{ __('ICO or PNG, up to 512KB. Recommended: 32x32 or 16x16 pixels') }}
                                        </p>
                                    </div>
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __('Select Favicon') }}
                                        <input type="file" wire:model="favicon" accept="image/x-icon,image/png" class="hidden">
                                    </label>
                                </div>
                            @endif
                        </div>
                        <flux:description class="mt-2">
                            {{ __('Upload your website favicon (the icon shown in browser tabs). Recommended size: 32x32 or 16x16 pixels.') }}
                        </flux:description>
                        <flux:error name="favicon" />
                        <div wire:loading wire:target="favicon" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                            {{ __('Uploading...') }}
                        </div>
                    </flux:field>
                </div>
            </div>

            <!-- Appearance Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/20">
                        <flux:icon.language class="size-5 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Appearance') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Control how content is displayed to visitors') }}</flux:text>
                    </div>
                </div>

                <flux:field>
                    <flux:label>{{ __('Frontend Text Size') }}</flux:label>
                    <flux:radio.group wire:model.live="frontend_text_size" variant="segmented">
                        <flux:radio value="xs" label="{{ __('Extra Small') }}" />
                        <flux:radio value="sm" label="{{ __('Small') }}" />
                        <flux:radio value="medium" label="{{ __('Medium') }}" />
                        <flux:radio value="lg" label="{{ __('Large') }}" />
                        <flux:radio value="xl" label="{{ __('Extra Large') }}" />
                        <flux:radio value="xxl" label="{{ __('Extra Extra Large') }}" />
                        <flux:radio value="custom" label="{{ __('Dynamic') }}" />
                    </flux:radio.group>
                    <flux:description>{{ __('Adjusts the base text size across the storefront (product pages, shop, and landing pages) for all visitors.') }}</flux:description>
                    <flux:error name="frontend_text_size" />
                </flux:field>

                @if($frontend_text_size === 'custom')
                    <flux:field>
                        <flux:label>{{ __('Dynamic Size') }} ({{ $frontend_text_size_custom }}%)</flux:label>
                        <input
                            type="range"
                            wire:model.live="frontend_text_size_custom"
                            min="50"
                            max="200"
                            step="5"
                            class="w-full h-2 rounded-full bg-neutral-200 dark:bg-neutral-700 accent-blue-600 cursor-pointer"
                        >
                        <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                            <span>50%</span>
                            <span>100%</span>
                            <span>200%</span>
                        </div>
                        <flux:description>{{ __('Set an exact base text size as a percentage of the default. 100% is the standard size.') }}</flux:description>
                        <flux:error name="frontend_text_size_custom" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('Frontend Content Width') }}</flux:label>
                    <flux:radio.group wire:model.live="frontend_content_width" variant="segmented">
                        <flux:radio value="narrow" label="{{ __('Narrow') }}" />
                        <flux:radio value="medium" label="{{ __('Medium') }}" />
                        <flux:radio value="wide" label="{{ __('Wide') }}" />
                        <flux:radio value="xl" label="{{ __('Extra Wide') }}" />
                        <flux:radio value="xxl" label="{{ __('Extra Extra Wide') }}" />
                        <flux:radio value="full" label="{{ __('Full') }}" />
                        <flux:radio value="custom" label="{{ __('Dynamic') }}" />
                    </flux:radio.group>
                    <flux:description>{{ __('Controls the maximum width of the main content area across the storefront (navigation, sections, shop, and product pages).') }}</flux:description>
                    <flux:error name="frontend_content_width" />
                </flux:field>

                @if($frontend_content_width === 'custom')
                    <flux:field>
                        <flux:label>{{ __('Dynamic Width') }} ({{ $frontend_content_width_custom }}px)</flux:label>
                        <input
                            type="range"
                            wire:model.live="frontend_content_width_custom"
                            min="960"
                            max="1920"
                            step="10"
                            class="w-full h-2 rounded-full bg-neutral-200 dark:bg-neutral-700 accent-blue-600 cursor-pointer"
                        >
                        <div class="flex justify-between text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                            <span>960px</span>
                            <span>1440px</span>
                            <span>1920px</span>
                        </div>
                        <flux:description>{{ __('Set an exact maximum content width in pixels.') }}</flux:description>
                        <flux:error name="frontend_content_width_custom" />
                    </flux:field>
                @endif
            </div>

            <!-- Contact Information Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/20">
                        <svg class="size-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Contact Information') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Contact details displayed on your website') }}</flux:text>
                    </div>
                </div>

                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Contact Email') }}</flux:label>
                        <flux:input
                            wire:model="contact_email"
                            type="email"
                            placeholder="info@example.com"
                        />
                        <flux:description>{{ __('Email address shown in the contact section') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Contact Phone') }}</flux:label>
                        <flux:input
                            wire:model="contact_phone"
                            type="text"
                            placeholder="+880 XXXX-XXXXXX"
                        />
                        <flux:description>{{ __('Phone number displayed for customer inquiries') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Contact Address') }}</flux:label>
                        <flux:textarea
                            wire:model="contact_address"
                            placeholder="{{ __('Enter your business address') }}"
                            rows="2"
                        />
                        <flux:description>{{ __('Physical address or location information') }}</flux:description>
                    </flux:field>
                </div>
            </div>

            <!-- Social Media Links Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/20">
                        <svg class="size-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Social Media Links') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Connect your social media profiles') }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label>{{ __('Facebook URL') }}</flux:label>
                        <flux:input
                            wire:model="social_facebook"
                            type="url"
                            placeholder="https://facebook.com/yourpage"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Instagram URL') }}</flux:label>
                        <flux:input
                            wire:model="social_instagram"
                            type="url"
                            placeholder="https://instagram.com/yourpage"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Twitter/X URL') }}</flux:label>
                        <flux:input
                            wire:model="social_twitter"
                            type="url"
                            placeholder="https://twitter.com/yourpage"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('LinkedIn URL') }}</flux:label>
                        <flux:input
                            wire:model="social_linkedin"
                            type="url"
                            placeholder="https://linkedin.com/company/yourpage"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('YouTube URL') }}</flux:label>
                        <flux:input
                            wire:model="social_youtube"
                            type="url"
                            placeholder="https://youtube.com/@yourchannel"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('TikTok URL') }}</flux:label>
                        <flux:input
                            wire:model="social_tiktok"
                            type="url"
                            placeholder="https://tiktok.com/@yourusername"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Pinterest URL') }}</flux:label>
                        <flux:input
                            wire:model="social_pinterest"
                            type="url"
                            placeholder="https://pinterest.com/yourusername"
                        />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('WhatsApp Number') }}</flux:label>
                        <flux:input
                            wire:model="social_whatsapp"
                            type="text"
                            placeholder="+1234567890"
                        />
                        <flux:description>{{ __('Enter your WhatsApp number with country code (e.g., +1234567890)') }}</flux:description>
                    </flux:field>
                </div>
            </div>

            <!-- Analytics & Tracking Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/20">
                        <svg class="size-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Analytics & Tracking') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Configure tracking and analytics tools') }}</flux:text>
                    </div>
                </div>

                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Facebook Pixel ID') }}</flux:label>
                        <flux:input
                            wire:model="facebook_pixel_id"
                            type="text"
                            placeholder="123456789012345"
                        />
                        <flux:description>{{ __('Enter your Facebook Pixel ID to track conversions and events. You can find this in your Facebook Events Manager.') }}</flux:description>
                        <flux:error name="facebook_pixel_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Google Analytics ID') }}</flux:label>
                        <flux:input
                            wire:model="google_analytics_id"
                            type="text"
                            placeholder="G-XXXXXXXXXX or UA-XXXXXX-X"
                        />
                        <flux:description>{{ __('Enter your Google Analytics 4 (G-XXXXXXXXXX) or Universal Analytics (UA-XXXXXX-X) ID') }}</flux:description>
                        <flux:error name="google_analytics_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Google Tag Manager ID') }}</flux:label>
                        <flux:input
                            wire:model="google_tag_manager_id"
                            type="text"
                            placeholder="GTM-XXXXXXX"
                        />
                        <flux:description>{{ __('Enter your Google Tag Manager container ID (format: GTM-XXXXXXX)') }}</flux:description>
                        <flux:error name="google_tag_manager_id" />
                    </flux:field>
                </div>
            </div>

            <!-- Site Verification Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/20">
                        <flux:icon.shield-check class="size-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('Site Verification') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Verify your website ownership with search engines') }}</flux:text>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <flux:field>
                        <flux:label>{{ __('Google Search Console Verification Code') }}</flux:label>
                        <flux:input
                            wire:model="google_verification_code"
                            type="text"
                            placeholder="Enter verification code"
                        />
                        <flux:description>{{ __('Enter the meta tag content value from Google Search Console') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Bing Webmaster Tools Verification Code') }}</flux:label>
                        <flux:input
                            wire:model="bing_verification_code"
                            type="text"
                            placeholder="Enter verification code"
                        />
                        <flux:description>{{ __('Enter the meta tag content value from Bing Webmaster Tools') }}</flux:description>
                    </flux:field>
                </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-900 p-6 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                        <flux:icon.magnifying-glass class="size-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('SEO Settings') }}</flux:heading>
                        <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Optimize your website for search engines') }}</flux:text>
                    </div>
                </div>

                <div class="space-y-5">
                    <flux:field>
                        <flux:label>{{ __('Meta Description') }}</flux:label>
                        <flux:textarea
                            wire:model.live="meta_description"
                            placeholder="{{ __('A brief description for search engines (recommended: 150-160 characters)') }}"
                            rows="3"
                        />
                        <div class="flex items-center justify-between mt-1">
                            <flux:description>{{ __('This description appears in search engine results. Keep it between 150-160 characters for optimal display.') }}</flux:description>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium {{ $this->metaDescriptionColor }}">
                                    <span wire:ignore>{{ $this->metaDescriptionLength }}</span> / 160
                                </span>
                                @if($this->metaDescriptionLength > 0 && $this->metaDescriptionLength < 120)
                                    <span class="text-xs text-yellow-600 dark:text-yellow-400" title="{{ __('Too short - aim for 120-160 characters') }}">
                                        ⚠️
                                    </span>
                                @elseif($this->metaDescriptionLength > 160)
                                    <span class="text-xs text-red-600 dark:text-red-400" title="{{ __('Too long - may be truncated in search results') }}">
                                        ⚠️
                                    </span>
                                @elseif($this->metaDescriptionLength >= 120 && $this->metaDescriptionLength <= 160)
                                    <span class="text-xs text-green-600 dark:text-green-400" title="{{ __('Optimal length') }}">
                                        ✓
                                    </span>
                                @endif
                            </div>
                        </div>
                        <flux:error name="meta_description" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Meta Keywords') }}</flux:label>
                        <flux:textarea
                            wire:model="meta_keywords"
                            placeholder="{{ __('Comma-separated keywords for SEO') }}"
                            rows="2"
                        />
                        <flux:description>{{ __('Enter relevant keywords separated by commas to help search engines understand your content') }}</flux:description>
                        <flux:error name="meta_keywords" />
                    </flux:field>

                    <!-- Open Graph Image -->
                    <flux:field>
                        <flux:label>{{ __('Open Graph Image') }}</flux:label>
                        <div
                            x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    e.preventDefault();
                                    this.isDragging = false;
                                    if (e.dataTransfer.files.length) {
                                        @this.upload('og_image', e.dataTransfer.files[0]);
                                    }
                                },
                                handleDragOver(e) {
                                    e.preventDefault();
                                    this.isDragging = true;
                                },
                                handleDragLeave() {
                                    this.isDragging = false;
                                }
                            }"
                            @drop.prevent="handleDrop"
                            @dragover.prevent="handleDragOver"
                            @dragleave.prevent="handleDragLeave"
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-neutral-300 dark:border-neutral-600'"
                            class="relative border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                        >
                            @if($existing_og_image || $og_image)
                                <div class="space-y-3">
                                    @if($og_image)
                                        <img src="{{ $og_image->temporaryUrl() }}" alt="OG Image Preview" class="mx-auto max-h-32 object-contain rounded">
                                    @elseif($existing_og_image)
                                        <img src="{{ asset('storage/'.$existing_og_image) }}" alt="Current OG Image" class="mx-auto max-h-32 object-contain rounded">
                                    @endif
                                    <div class="flex items-center justify-center gap-3">
                                        <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            {{ __('Change') }}
                                            <input type="file" wire:model="og_image" accept="image/*" class="hidden">
                                        </label>
                                        @if($existing_og_image)
                                            <button type="button" wire:click="removeOgImage" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                {{ __('Remove') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-neutral-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">
                                            <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ __('Drop image here') }}</span>
                                            {{ __('or click to browse') }}
                                        </p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-500">
                                            {{ __('PNG, JPG, GIF up to 2MB. Recommended: 1200x630 pixels for social sharing') }}
                                        </p>
                                    </div>
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __('Select Image') }}
                                        <input type="file" wire:model="og_image" accept="image/*" class="hidden">
                                    </label>
                                </div>
                            @endif
                        </div>
                        <flux:description class="mt-2">
                            {{ __('Upload an image that will be displayed when your website is shared on social media platforms (Facebook, Twitter, LinkedIn, etc.). Recommended size: 1200x630 pixels.') }}
                        </flux:description>
                        <flux:error name="og_image" />
                        <div wire:loading wire:target="og_image" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                            {{ __('Uploading...') }}
                        </div>
                    </flux:field>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-between gap-4 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                @if (session()->has('message'))
                    <flux:callout variant="success" class="flex-1 mb-0">{{ session('message') }}</flux:callout>
                @else
                    <div class="flex-1"></div>
                @endif
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="whitespace-nowrap">
                    <span wire:loading.remove wire:target="update">{{ __('Save All Settings') }}</span>
                    <span wire:loading wire:target="update">{{ __('Saving...') }}</span>
                </flux:button>
            </div>
        </form>
</div>
