<div
    x-data="{
        showCalculator: false,
        showQuickActions: false,
        calc: { display: '0', stored: null, operator: null, replace: false },
        calcInput(value) {
            if (this.calc.replace) { this.calc.display = '0'; this.calc.replace = false; }
            this.calc.display = this.calc.display === '0' ? String(value) : this.calc.display + value;
        },
        calcDecimal() {
            if (this.calc.replace) { this.calc.display = '0'; this.calc.replace = false; }
            if (! this.calc.display.includes('.')) { this.calc.display += '.'; }
        },
        calcOperator(op) {
            if (this.calc.operator && ! this.calc.replace) { this.calcEquals(); }
            this.calc.stored = parseFloat(this.calc.display);
            this.calc.operator = op;
            this.calc.replace = true;
        },
        calcEquals() {
            if (! this.calc.operator || this.calc.stored === null) return;
            const current = parseFloat(this.calc.display);
            let result = current;
            if (this.calc.operator === '+') result = this.calc.stored + current;
            if (this.calc.operator === '-') result = this.calc.stored - current;
            if (this.calc.operator === '×') result = this.calc.stored * current;
            if (this.calc.operator === '÷') result = current === 0 ? 0 : this.calc.stored / current;
            this.calc.display = String(Math.round(result * 100) / 100);
            this.calc.operator = null;
            this.calc.stored = null;
            this.calc.replace = true;
        },
        calcClear() {
            this.calc.display = '0';
            this.calc.stored = null;
            this.calc.operator = null;
            this.calc.replace = false;
        },
        handleKey(e) {
            if (e.key === 'Escape') {
                if ($wire.showCloseShiftForm) { $wire.set('showCloseShiftForm', false); return; }
                if ($wire.variantPickerProductId) { $wire.set('variantPickerProductId', null); return; }
                if ($wire.viewProductId) { $wire.closeProductDetails(); return; }
                if ($wire.reprintOrderId) { $wire.closeReprint(); return; }
                this.showCalculator = false;
                this.showQuickActions = false;
                return;
            }
            if (e.ctrlKey && e.key.toLowerCase() === 'p') { e.preventDefault(); window.print(); return; }
            if (e.ctrlKey && e.key.toLowerCase() === 'f') { e.preventDefault(); this.$refs.posSearch?.focus(); return; }

            const tag = (e.target.tagName || '').toLowerCase();
            const typing = tag === 'input' || tag === 'textarea';
            if (e.key === '/' && ! typing) { e.preventDefault(); this.$refs.posSearch?.focus(); return; }

            switch (e.key) {
                case 'F2': e.preventDefault(); this.$refs.customerPhoneInput?.focus(); break;
                case 'F3': e.preventDefault(); this.$refs.discountInput?.focus(); break;
                case 'F4': e.preventDefault(); this.$refs.paymentTenderedInput?.focus(); break;
                case 'F5': e.preventDefault(); $wire.holdSale(); break;
                case 'F7': e.preventDefault(); if ($wire.completedOrderId) { $wire.startNewSale(); } break;
            }
        },
    }"
    @keydown.window="handleKey($event)"
    class="flex min-h-screen flex-col bg-zinc-100 dark:bg-zinc-950"
>
    {{-- Top bar --}}
    <div class="flex items-center justify-between gap-4 border-b border-zinc-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-900 print:hidden">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m10-7V6a2 2 0 00-2-2H5a2 2 0 00-2 2v3m18 0v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9m18 0H3"></path></svg>
            </div>
            <div>
                <flux:heading size="lg" class="leading-tight">{{ __('POS') }}</flux:heading>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $this->register()->name }}</span>
            </div>
            @if ($this->shift())
                <flux:badge variant="success" size="sm">
                    <span class="inline-flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ __('Shift open') }}
                    </span>
                </flux:badge>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden text-sm text-zinc-500 sm:inline dark:text-zinc-400">{{ auth()->user()->name }}</span>
            @if ($this->shift())
                @can('close pos shift')
                    <flux:button size="sm" variant="ghost" wire:click="confirmCloseShift">{{ __('Close Shift') }}</flux:button>
                @endcan
            @endif
            <flux:button size="sm" variant="ghost" :href="route('dashboard')" wire:navigate>{{ __('Exit') }}</flux:button>
        </div>
    </div>

    <div class="flex-1 p-4 print:p-0">
        {{-- Auto-dismissing flash messages --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.opacity.duration.300ms class="mb-4 print:hidden">
                <flux:callout variant="success">{{ session('message') }}</flux:callout>
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition.opacity.duration.300ms class="mb-4 print:hidden">
                <flux:callout variant="danger">{{ session('error') }}</flux:callout>
            </div>
        @endif

        {{-- No open shift: require one before selling --}}
        @if (! $this->shift())
            <div class="mx-auto mt-16 max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <svg class="h-6 w-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m10-7V6a2 2 0 00-2-2H5a2 2 0 00-2 2v3m18 0v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9m18 0H3"></path></svg>
                    </div>
                    <flux:heading>{{ __('Open a Shift to Start Selling') }}</flux:heading>
                </div>
                @can('open pos shift')
                    <form wire:submit.prevent="openShift" class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Opening Cash Float') }}</flux:label>
                            <flux:input type="number" step="0.01" min="0" wire:model="openingCash" class="text-lg" />
                            <flux:error name="openingCash" />
                        </flux:field>
                        <flux:button type="submit" variant="primary" class="w-full !min-h-[44px]">{{ __('Open Shift') }}</flux:button>
                    </form>
                @else
                    <flux:callout variant="warning">{{ __('You do not have permission to open a shift. Ask a manager.') }}</flux:callout>
                @endcan
            </div>

        {{-- Receipt after a completed sale --}}
        @elseif ($this->completedOrder())
            @php($order = $this->completedOrder())
            <div class="mx-auto max-w-md rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-4 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <flux:heading size="lg">{{ __('Sale Complete') }}</flux:heading>
                    <p class="font-mono text-sm text-zinc-500 dark:text-zinc-400">{{ $order->order_number }}</p>
                </div>

                @include('livewire.pos.partials.receipt', ['order' => $order])

                <div class="mt-4 flex gap-2 print:hidden">
                    <flux:button variant="primary" class="flex-1 !min-h-[44px]" onclick="window.print()">
                        <span class="inline-flex items-center justify-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4H8v4a1 1 0 001 1zm8-12V5a1 1 0 00-1-1H8a1 1 0 00-1 1v4h10z"></path></svg>
                            {{ __('Print Receipt') }}
                        </span>
                    </flux:button>
                    <flux:button variant="ghost" class="flex-1 !min-h-[44px]" wire:click="startNewSale">{{ __('New Sale') }} <span class="ml-1 hidden text-xs opacity-60 sm:inline">(F7)</span></flux:button>
                </div>
            </div>

        {{-- Main till --}}
        @else
            <div class="flex flex-col gap-4 xl:flex-row">
                {{-- Category rail (desktop/ultra-wide) --}}
                @if ($search === '' && $this->hasAnyCategories())
                    <div class="hidden xl:flex xl:w-56 xl:shrink-0 xl:flex-col xl:gap-1 xl:self-start xl:rounded-xl xl:border xl:border-zinc-200 xl:bg-white xl:p-2 dark:xl:border-zinc-800 dark:xl:bg-zinc-900">
                        <div class="relative mb-1">
                            <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input
                                type="text"
                                wire:model.live.debounce.200ms="categorySearch"
                                placeholder="{{ __('Search categories...') }}"
                                class="w-full min-h-[38px] rounded-lg border-zinc-300 pl-8 {{ $categorySearch !== '' ? 'pr-8' : '' }} text-sm focus:border-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:focus:border-white dark:focus:ring-white"
                            />
                            @if ($categorySearch !== '')
                                <button type="button" wire:click="$set('categorySearch', '')" class="absolute right-2 top-1/2 flex h-5 w-5 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-200" aria-label="{{ __('Clear category search') }}">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                        </div>
                        <button type="button" wire:click="selectCategory(null)" class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors duration-150 {{ $categoryFilter === null ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                {{ __('All Products') }}
                            </span>
                            <span class="text-xs opacity-70">{{ $this->totalActiveProductsCount() }}</span>
                        </button>
                        @foreach ($this->categories() as $category)
                            <button type="button" wire:click="selectCategory({{ $category->id }})" class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors duration-150 {{ $categoryFilter === $category->id ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                                <span class="flex min-w-0 items-center gap-2">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 7a4 4 0 014-4h4.586a2 2 0 011.414.586l7 7a2 2 0 010 2.828l-6.586 6.586a2 2 0 01-2.828 0l-7-7A2 2 0 013 11V7z"></path></svg>
                                    <span class="truncate">{{ $category->name_en }}</span>
                                </span>
                                <span class="shrink-0 text-xs opacity-70">{{ $this->categoryCounts()[$category->id] ?? 0 }}</span>
                            </button>
                        @endforeach
                        @if ($categorySearch !== '' && $this->categories()->isEmpty())
                            <p class="px-3 py-2 text-sm text-zinc-400 dark:text-zinc-500">{{ __('No categories found.') }}</p>
                        @endif
                    </div>
                @endif

                <div class="grid flex-1 gap-4 lg:grid-cols-5">
                    {{-- Left: search + products --}}
                    <div class="lg:col-span-3 space-y-4">
                        <div class="flex gap-2">
                            <div
                                x-data
                                @pos-item-added.window="$nextTick(() => $refs.posSearch.focus())"
                                class="relative flex-1"
                            >
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input
                                    x-ref="posSearch"
                                    type="text"
                                    wire:model.live.debounce.200ms="search"
                                    wire:keydown.enter="scanBarcode($event.target.value)"
                                    placeholder="{{ __('Scan barcode, or search by name / SKU... (Ctrl+F)') }}"
                                    autofocus
                                    class="w-full min-h-[44px] rounded-lg border-zinc-300 pl-10 text-base focus:border-zinc-900 focus:ring-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:focus:border-white dark:focus:ring-white"
                                />
                            </div>

                            {{-- Grid / list density toggle --}}
                            <div class="flex items-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                <button type="button" wire:click="toggleViewMode" wire:target="toggleViewMode" aria-pressed="{{ $viewMode === 'grid' ? 'true' : 'false' }}" aria-label="{{ __('Grid view') }}" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-l-lg {{ $viewMode === 'grid' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                </button>
                                <button type="button" wire:click="toggleViewMode" wire:target="toggleViewMode" aria-pressed="{{ $viewMode === 'list' ? 'true' : 'false' }}" aria-label="{{ __('List view') }}" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-r-lg {{ $viewMode === 'list' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Category chips (below xl, or as a fallback) --}}
                        @if ($search === '' && $this->categories()->isNotEmpty())
                            <div class="flex flex-wrap gap-2 xl:hidden">
                                <button type="button" wire:click="selectCategory(null)" class="cursor-pointer rounded-full px-3 py-1.5 text-sm font-medium transition-colors duration-150 {{ $categoryFilter === null ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-white text-zinc-600 ring-1 ring-zinc-200 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800' }}">
                                    {{ __('All') }}
                                </button>
                                @foreach ($this->categories() as $category)
                                    <button type="button" wire:click="selectCategory({{ $category->id }})" class="cursor-pointer rounded-full px-3 py-1.5 text-sm font-medium transition-colors duration-150 {{ $categoryFilter === $category->id ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-white text-zinc-600 ring-1 ring-zinc-200 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800' }}">
                                        {{ $category->name_en }}
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- Variant picker --}}
                        @if ($this->variantPickerProduct())
                            <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="mb-3 flex items-center justify-between">
                                    <flux:text class="font-medium">{{ __('Choose a variant of :name', ['name' => $this->variantPickerProduct()->name_en]) }}</flux:text>
                                    <button type="button" wire:click="$set('variantPickerProductId', null)" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800" aria-label="{{ __('Cancel (Esc)') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    @foreach ($this->variantPickerProduct()->productAttributes->where('is_active', true) as $variant)
                                        <button type="button" wire:click="addVariantToCart({{ $variant->id }})" class="min-h-[64px] cursor-pointer rounded-lg border border-zinc-200 p-3 text-left text-sm transition-colors duration-150 hover:border-zinc-900 hover:bg-zinc-50 active:scale-[0.98] dark:border-zinc-700 dark:hover:border-white dark:hover:bg-zinc-800">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $variant->attribute_label }}</div>
                                            <div class="text-zinc-500 dark:text-zinc-400">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($variant->price, 2) }} &middot; {{ __('stock') }}: {{ $variant->stock }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Product grid / list --}}
                            @if ($viewMode === 'grid')
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                                    @forelse ($this->searchResults() as $product)
                                        <x-pos.product-card :product="$product" />
                                    @empty
                                        <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                                            {{ __('No matching products.') }}
                                        </div>
                                    @endforelse
                                </div>
                            @else
                                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                                    @forelse ($this->searchResults() as $product)
                                        <x-pos.product-list-row :product="$product" />
                                    @empty
                                        <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ __('No matching products.') }}
                                        </div>
                                    @endforelse
                                </div>
                            @endif

                            @if ($this->hasMoreProducts())
                                <div class="flex justify-center">
                                    <flux:button variant="ghost" wire:click="loadMoreProducts" wire:loading.attr="disabled" wire:target="loadMoreProducts">
                                        <span wire:loading.remove wire:target="loadMoreProducts">{{ __('Load More') }}</span>
                                        <span wire:loading wire:target="loadMoreProducts">{{ __('Loading...') }}</span>
                                    </flux:button>
                                </div>
                            @endif
                        @endif

                        {{-- Recently sold --}}
                        @if ($search === '' && $this->recentlySold()->isNotEmpty())
                            <div x-data="{ open: true }" class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                <button type="button" @click="open = ! open" class="flex w-full cursor-pointer items-center justify-between text-left">
                                    <flux:text class="font-medium">{{ __('Recently Sold') }}</flux:text>
                                    <svg class="h-4 w-4 text-zinc-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div x-show="open" x-collapse class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                                    @foreach ($this->recentlySold() as $product)
                                        <x-pos.product-card :product="$product" />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($this->heldSales()->isNotEmpty())
                            <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                                <flux:text class="mb-2 font-medium">{{ __('Held Sales') }}</flux:text>
                                <div class="space-y-2">
                                    @foreach ($this->heldSales() as $held)
                                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-2.5 text-sm dark:border-zinc-700">
                                            <span>{{ $held->note ?: __('Held :time', ['time' => $held->held_at->diffForHumans()]) }}</span>
                                            <div class="flex gap-1">
                                                <flux:button size="sm" variant="ghost" wire:click="resumeHeldSale({{ $held->id }})">{{ __('Resume') }}</flux:button>
                                                <x-admin.confirm-delete-button message="{{ __('Discard this held sale?') }}" wire:click="discardHeldSale({{ $held->id }})" size="sm">{{ __('Discard') }}</x-admin.confirm-delete-button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Right: cart, customer, payment --}}
                    <div class="lg:col-span-2 space-y-4">
                        <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <flux:text class="mb-2 font-medium">{{ __('Cart') }}</flux:text>

                            @forelse ($cart as $key => $line)
                                <x-pos.cart-line :line-key="$key" :line="$line" />
                            @empty
                                <p class="py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('Cart is empty. Scan or search to add products.') }}</p>
                            @endforelse

                            @if (! empty($cart))
                                <div class="mt-2 flex justify-end print:hidden">
                                    @can('hold pos sales')
                                        <flux:button size="sm" variant="ghost" wire:click="holdSale">{{ __('Hold Sale') }} <span class="ml-1 text-xs opacity-60">(F5)</span></flux:button>
                                    @endcan
                                </div>
                            @endif
                        </div>

                        {{-- Undo-removal toast --}}
                        @if ($lastRemovedLine)
                            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity.duration.300ms class="flex items-center justify-between rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                <span>{{ __('Removed :name', ['name' => $lastRemovedLine['line']['product_name']]) }}</span>
                                <flux:button size="sm" variant="ghost" wire:click="undoRemoveLine">{{ __('Undo') }}</flux:button>
                            </div>
                        @endif

                        {{-- Customer --}}
                        <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <flux:text class="mb-2 font-medium">{{ __('Customer') }}</flux:text>
                            @if ($this->selectedCustomer())
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium">{{ $this->selectedCustomer()->name }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $this->selectedCustomer()->phone }} &middot; {{ __('Store credit') }}: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->selectedCustomer()->store_credit_balance, 2) }}</div>
                                    </div>
                                    <flux:button size="sm" variant="ghost" wire:click="clearCustomer">{{ __('Walk-in') }}</flux:button>
                                </div>
                            @elseif ($showCustomerForm)
                                <div class="space-y-2">
                                    <flux:input wire:model="customerName" placeholder="{{ __('Customer name') }}" class="min-h-[44px]" />
                                    <flux:error name="customerName" />
                                    <div class="flex gap-2">
                                        <flux:button size="sm" variant="primary" wire:click="createCustomer">{{ __('Create & Select') }}</flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="$set('showCustomerForm', false)">{{ __('Cancel') }}</flux:button>
                                    </div>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <flux:input x-ref="customerPhoneInput" wire:model="customerPhone" placeholder="{{ __('Phone number (optional, F2)') }}" class="min-h-[44px]" />
                                    <flux:button wire:click="findCustomer" class="!min-h-[44px]">{{ __('Find') }}</flux:button>
                                </div>
                                <flux:error name="customerPhone" />
                            @endif
                        </div>

                        {{-- Discount / coupon / notes --}}
                        @canany(['apply pos discounts'])
                            <div class="rounded-xl border border-zinc-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900 space-y-2">
                                <flux:field>
                                    <flux:label>{{ __('Manual Discount') }}</flux:label>
                                    <flux:input x-ref="discountInput" type="number" step="0.01" min="0" wire:model.live="discountAmount" class="min-h-[44px]" />
                                </flux:field>
                                @if ($appliedCouponId)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            {{ __('Coupon applied') }}
                                        </span>
                                        <flux:button size="sm" variant="ghost" wire:click="removeCoupon">{{ __('Remove') }}</flux:button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <flux:input wire:model="couponCode" placeholder="{{ __('Coupon code') }}" class="min-h-[44px]" />
                                        <flux:button wire:click="applyCoupon" class="!min-h-[44px]">{{ __('Apply') }}</flux:button>
                                    </div>
                                @endif
                                <flux:field>
                                    <flux:label>{{ __('Notes') }}</flux:label>
                                    <flux:textarea wire:model="notes" rows="2" placeholder="{{ __('Optional note for this sale') }}" />
                                </flux:field>
                            </div>
                        @endcanany

                        <div class="space-y-4 lg:sticky lg:bottom-4">
                            {{-- Totals --}}
                            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 space-y-1.5 text-sm">
                                <div class="flex justify-between"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Subtotal') }}</span><span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->subtotal(), 2) }}</span></div>
                                <div class="flex justify-between"><span class="text-zinc-500 dark:text-zinc-400">{{ __('Discount') }}</span><span class="tabular-nums">-{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->totalDiscount(), 2) }}</span></div>
                                <div class="flex items-baseline justify-between border-t border-zinc-100 pt-2 dark:border-zinc-800">
                                    <span class="font-semibold text-zinc-900 dark:text-white">{{ __('Total') }}</span>
                                    <span class="text-2xl font-bold tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->total(), 2) }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-500 dark:text-zinc-400"><span>{{ __('Paid') }}</span><span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->paidSoFar(), 2) }}</span></div>
                                <div class="flex items-center justify-between font-semibold {{ $this->remainingDue() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    <span>{{ __('Remaining') }}</span>
                                    <span class="text-lg tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->remainingDue(), 2) }}</span>
                                </div>
                            </div>

                            {{-- Payments --}}
                            <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 space-y-3">
                                <flux:text class="font-medium">{{ __('Payment') }}</flux:text>

                                @foreach ($payments as $index => $payment)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="capitalize">{{ str_replace('_', ' ', $payment['method']) }}</span>
                                        <span class="tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($payment['amount'], 2) }}</span>
                                        <button type="button" wire:click="removePayment({{ $index }})" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40" aria-label="{{ __('Remove payment') }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @endforeach

                                @if ($this->remainingDue() > 0)
                                    <div class="space-y-2">
                                        <div class="grid grid-cols-4 gap-1.5">
                                            @foreach ([
                                                'cash' => __('Cash'),
                                                'card' => __('Card'),
                                                'mobile_banking' => __('Mobile'),
                                                'store_credit' => __('Credit'),
                                            ] as $method => $label)
                                                <button
                                                    type="button"
                                                    wire:click="$set('paymentMethod', '{{ $method }}')"
                                                    class="min-h-[44px] cursor-pointer rounded-lg border text-xs font-medium transition-colors duration-150 {{ $paymentMethod === $method ? 'border-zinc-900 bg-zinc-900 text-white dark:border-white dark:bg-white dark:text-zinc-900' : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                                                >
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>

                                        @if ($paymentMethod === 'cash' && ! empty($this->quickCashOptions()))
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($this->quickCashOptions() as $amount)
                                                    <button type="button" wire:click="quickCash({{ $amount }})" class="min-h-[36px] cursor-pointer rounded-full border border-zinc-200 px-3 text-xs font-medium tabular-nums text-zinc-700 transition-colors duration-150 hover:border-zinc-900 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-white dark:hover:bg-zinc-800">
                                                        {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($amount, 2) }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="flex flex-wrap gap-2">
                                            <flux:input x-ref="paymentTenderedInput" type="number" step="0.01" min="0" wire:model="paymentTendered" placeholder="{{ __('Amount (F4)') }}" class="min-h-[44px] w-32" />
                                            @if (in_array($paymentMethod, ['card', 'mobile_banking']))
                                                <flux:input wire:model="paymentReference" placeholder="{{ __('Reference #') }}" class="min-h-[44px] w-32" />
                                            @endif
                                            <flux:button variant="primary" wire:click="addPayment" class="!min-h-[44px] flex-1">{{ __('Add Payment') }}</flux:button>
                                        </div>
                                    </div>
                                @endif

                                <flux:button variant="primary" class="w-full !min-h-[48px] text-base" wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout" :disabled="$this->remainingDue() > 0 || empty($cart)">
                                    <span wire:loading.remove wire:target="checkout">{{ __('Complete Sale') }}</span>
                                    <span wire:loading wire:target="checkout">{{ __('Processing...') }}</span>
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions floating menu --}}
            <div class="fixed bottom-4 left-4 z-30 print:hidden" @click.outside="showQuickActions = false">
                <div x-show="showQuickActions" x-transition.origin.bottom.left class="mb-2 w-56 space-y-1 rounded-xl border border-zinc-200 bg-white p-2 shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                    @can('hold pos sales')
                        <button type="button" wire:click="holdSale" @click="showQuickActions = false" class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('Hold Sale') }} <span class="ml-auto text-xs opacity-50">F5</span>
                        </button>
                    @endcan
                    @can('void pos sale line')
                        <button type="button" wire:confirm="{{ __('Clear the entire cart? This cannot be undone.') }}" wire:click="voidTransaction" @click="showQuickActions = false" class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            {{ __('Void Transaction') }}
                        </button>
                    @endcan
                    @if ($lastCompletedOrderId)
                        <button type="button" wire:click="reprintLastReceipt" @click="showQuickActions = false" class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4H8v4a1 1 0 001 1zm8-12V5a1 1 0 00-1-1H8a1 1 0 00-1 1v4h10z"></path></svg>
                            {{ __('Reprint Last Receipt') }}
                        </button>
                    @endif
                    <button type="button" @click="showCalculator = true; showQuickActions = false" class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m10-7V6a2 2 0 00-2-2H5a2 2 0 00-2 2v3m18 0v9a2 2 0 01-2 2H5a2 2 0 01-2-2v-9m18 0H3"></path></svg>
                        {{ __('Calculator') }}
                    </button>
                </div>
                <button type="button" @click="showQuickActions = ! showQuickActions" :aria-expanded="showQuickActions" aria-label="{{ __('Quick actions') }}" class="flex h-14 w-14 cursor-pointer items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition-transform duration-150 hover:scale-105 active:scale-95 dark:bg-white dark:text-zinc-900">
                    <svg class="h-6 w-6 transition-transform duration-200" :class="showQuickActions ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
        @endif
    </div>

    {{-- Close shift modal --}}
    @if ($showCloseShiftForm)
        <flux:modal wire:model="showCloseShiftForm" name="close-shift-modal">
            <form wire:submit.prevent="closeShift" class="space-y-4">
                <flux:heading>{{ __('Close Shift') }}</flux:heading>
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Expected cash in drawer') }}: <span class="font-medium tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($this->expectedCash(), 2) }}</span>
                </div>
                <flux:field>
                    <flux:label>{{ __('Counted Cash') }}</flux:label>
                    <flux:input type="number" step="0.01" min="0" wire:model="closingCash" class="min-h-[44px]" />
                    <flux:error name="closingCash" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea wire:model="closeNotes" rows="2" />
                </flux:field>
                <div class="flex gap-4">
                    <flux:button type="submit" variant="primary" class="!min-h-[44px]">{{ __('Close Shift') }}</flux:button>
                    <flux:button type="button" variant="ghost" class="!min-h-[44px]" wire:click="$set('showCloseShiftForm', false)">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    {{--
        Reprint overlay — a plain Alpine-shown overlay (like the calculator
        below) rather than <flux:modal>, which manages its own open/close
        state via wire:model and would overwrite reprintOrderId with a plain
        true/false, destroying the actual order id. Esc and the explicit
        Close button both call closeReprint() to dismiss it.
    --}}
    @if ($this->reprintOrder())
        @php($reprint = $this->reprintOrder())
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4 print:hidden" wire:click.self="closeReprint">
            <div class="w-full max-w-md space-y-4 rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:heading>{{ __('Reprint Receipt') }}</flux:heading>
                    <span class="font-mono text-sm text-zinc-500 dark:text-zinc-400">{{ $reprint->order_number }}</span>
                </div>
                @include('livewire.pos.partials.receipt', ['order' => $reprint])
                <div class="flex gap-2">
                    <flux:button variant="primary" class="flex-1 !min-h-[44px]" onclick="window.print()">{{ __('Print') }}</flux:button>
                    <flux:button variant="ghost" class="flex-1 !min-h-[44px]" wire:click="closeReprint">{{ __('Close') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{--
        Product details overlay — same rationale as the reprint overlay above:
        a plain Alpine-shown div rather than <flux:modal>, since that binds
        wire:model to a boolean and would clobber viewProductId.
    --}}
    @if ($this->viewProduct())
        @php($viewedProduct = $this->viewProduct())
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4 print:hidden" wire:click.self="closeProductDetails">
            <div class="w-full max-w-sm space-y-4 rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-2">
                    <flux:heading size="lg">{{ __('Product Details') }}</flux:heading>
                    <button type="button" wire:click="closeProductDetails" class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="{{ __('Close (Esc)') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex h-40 items-center justify-center overflow-hidden rounded-lg bg-zinc-50 dark:bg-zinc-800">
                    @if ($viewedProduct->primary_image)
                        <img src="{{ asset('storage/'.$viewedProduct->primary_image) }}" alt="{{ $viewedProduct->name_en }}" class="h-full w-full object-cover">
                    @else
                        <svg class="h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    @endif
                </div>

                <div>
                    <div class="text-base font-semibold text-zinc-900 dark:text-white">{{ $viewedProduct->name_en }}</div>
                    @if ($viewedProduct->category)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $viewedProduct->category->name_en }}</div>
                    @endif
                </div>

                @if ($viewedProduct->description)
                    <p class="max-h-24 overflow-y-auto text-sm text-zinc-600 dark:text-zinc-300">{{ $viewedProduct->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 rounded-lg border border-zinc-200 p-3 text-sm dark:border-zinc-700">
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Price') }}</div>
                        <div class="font-semibold tabular-nums text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($viewedProduct->getSyncedPrice(), 2) }}</div>
                    </div>
                    @if ($viewedProduct->getSyncedCompareAtPrice())
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Compare at') }}</div>
                            <div class="font-medium tabular-nums text-zinc-400 line-through dark:text-zinc-500">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($viewedProduct->getSyncedCompareAtPrice(), 2) }}</div>
                        </div>
                    @endif
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Stock') }}</div>
                        <div class="font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $viewedProduct->getSyncedStock() }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('SKU') }}</div>
                        <div class="font-mono text-zinc-900 dark:text-white">{{ $viewedProduct->sku ?? '—' }}</div>
                    </div>
                    @if ($viewedProduct->barcode)
                        <div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Barcode') }}</div>
                            <div class="font-mono text-zinc-900 dark:text-white">{{ $viewedProduct->barcode }}</div>
                        </div>
                    @endif
                </div>

                @if ($viewedProduct->hasAttributes())
                    <div>
                        <div class="mb-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Variants') }}</div>
                        <div class="max-h-32 space-y-1 overflow-y-auto text-sm">
                            @foreach ($viewedProduct->productAttributes->where('is_active', true) as $variant)
                                <div class="flex justify-between gap-2 rounded-md bg-zinc-50 px-2.5 py-1.5 dark:bg-zinc-800">
                                    <span class="truncate">{{ $variant->attribute_label }}</span>
                                    <span class="shrink-0 tabular-nums">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($variant->price, 2) }} &middot; {{ $variant->stock }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex gap-2">
                    <flux:button variant="primary" class="flex-1 !min-h-[44px]" wire:click="addProductFromDetails({{ $viewedProduct->id }})" :disabled="! $viewedProduct->isInStock()">{{ __('Add to Cart') }}</flux:button>
                    <flux:button variant="ghost" class="flex-1 !min-h-[44px]" wire:click="closeProductDetails">{{ __('Close') }}</flux:button>
                </div>
            </div>
        </div>
    @endif

    {{-- Calculator (client-side only, no server round-trip) --}}
    <div x-show="showCalculator" x-transition.opacity.duration.150ms style="display: none" class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4 print:hidden" @click.self="showCalculator = false">
        <div x-show="showCalculator" x-transition class="w-full max-w-xs rounded-xl bg-white p-4 shadow-xl dark:bg-zinc-900">
            <div class="mb-3 flex items-center justify-between">
                <flux:heading size="sm">{{ __('Calculator') }}</flux:heading>
                <button type="button" @click="showCalculator = false" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800" aria-label="{{ __('Close (Esc)') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="mb-3 rounded-lg bg-zinc-100 px-3 py-4 text-right text-2xl font-semibold tabular-nums dark:bg-zinc-800" x-text="calc.display"></div>
            <div class="grid grid-cols-4 gap-1.5">
                <button type="button" @click="calcClear()" class="col-span-2 min-h-[44px] cursor-pointer rounded-lg bg-zinc-200 text-sm font-medium hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600">{{ __('Clear') }}</button>
                <button type="button" @click="calcOperator('÷')" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-200 text-lg font-medium hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600">÷</button>
                <button type="button" @click="calcOperator('×')" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-200 text-lg font-medium hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600">&times;</button>

                @foreach ([7, 8, 9] as $n)
                    <button type="button" @click="calcInput({{ $n }})" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-50 text-lg font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">{{ $n }}</button>
                @endforeach
                <button type="button" @click="calcOperator('-')" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-200 text-lg font-medium hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600">&minus;</button>

                @foreach ([4, 5, 6] as $n)
                    <button type="button" @click="calcInput({{ $n }})" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-50 text-lg font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">{{ $n }}</button>
                @endforeach
                <button type="button" @click="calcOperator('+')" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-200 text-lg font-medium hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600">+</button>

                @foreach ([1, 2, 3] as $n)
                    <button type="button" @click="calcInput({{ $n }})" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-50 text-lg font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">{{ $n }}</button>
                @endforeach
                <button type="button" @click="calcEquals()" class="row-span-2 min-h-[44px] cursor-pointer rounded-lg bg-zinc-900 text-lg font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">=</button>

                <button type="button" @click="calcInput(0)" class="col-span-2 min-h-[44px] cursor-pointer rounded-lg bg-zinc-50 text-lg font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">0</button>
                <button type="button" @click="calcDecimal()" class="min-h-[44px] cursor-pointer rounded-lg bg-zinc-50 text-lg font-medium hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700">.</button>
            </div>
        </div>
    </div>
</div>
