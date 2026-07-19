<div class="space-y-6">
    <x-admin.page-header :heading="__('Shipping Management')" :description="__('Configure shipping options and rates')" />

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Current Status Summary --}}
    @if ($setting && $setting->is_active)
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="md" class="mb-2">{{ __('Current Active Configuration') }}</flux:heading>
                    <div class="space-y-1">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            <span class="font-medium">{{ __('Mode:') }}</span>
                            <flux:badge variant="success" size="sm" class="ml-2">
                                {{ ucfirst($setting->type) }}
                            </flux:badge>
                        </p>
                        @if ($setting->type === 'flat')
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <span class="font-medium">{{ __('Rate:') }}</span>
                                <span class="ml-2 font-semibold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($setting->flat_rate ?? 0, 2) }}</span>
                            </p>
                        @else
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                <span class="font-medium">{{ __('Base Rate:') }}</span>
                                <span class="ml-2 font-semibold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($setting->base_rate ?? 0, 2) }}</span>
                                <span class="ml-4 font-medium">{{ __('Per KG:') }}</span>
                                <span class="ml-2 font-semibold text-zinc-900 dark:text-white">{{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($setting->per_kg_rate ?? 0, 2) }}</span>
                            </p>
                        @endif
                    </div>
                </div>
                <flux:badge variant="success" size="lg">{{ __('Active') }}</flux:badge>
            </div>
        </div>
    @else
        <flux:callout variant="warning">
            {{ __('No active shipping configuration. Please configure and activate a shipping mode below.') }}
        </flux:callout>
    @endif

    {{-- Shipping Mode Selection --}}
    <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                <flux:icon.truck class="size-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <flux:heading size="md" level="3">{{ __('Shipping Mode') }}</flux:heading>
                <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Choose how shipping costs are calculated for orders') }}</flux:text>
            </div>
        </div>

        <div class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Shipping Type') }}</flux:label>
                <flux:radio.group wire:model="type">
                    <flux:radio value="flat" label="{{ __('Flat Rate') }}" description="{{ __('One fixed rate for all orders') }}" />
                    <flux:radio value="weight" label="{{ __('Weight-Based') }}" description="{{ __('Dynamic pricing based on order weight') }}" />
                    <flux:radio value="city" label="{{ __('City + Weight Based') }}" description="{{ __('City-specific rates with weight adjustments') }}" />
                </flux:radio.group>
            </flux:field>

            {{-- Flat Rate Configuration --}}
            @if ($type === 'flat')
                <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <flux:field>
                        <flux:label>{{ __('Flat Rate') }} ({{ __('Currency') }})</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model="flatRate" placeholder="0.00" />
                        <flux:error name="flatRate" />
                        <flux:description>{{ __('This rate will be applied to all orders regardless of weight or location.') }}</flux:description>
                    </flux:field>
                </div>
            @endif

            {{-- Weight-Based Configuration --}}
            @if ($type === 'weight')
                <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <flux:field>
                        <flux:label>{{ __('Base Weight') }} (KG)</flux:label>
                        <flux:input type="number" step="0.01" min="0.01" wire:model.live="baseWeightKg" placeholder="1.00" />
                        <flux:error name="baseWeightKg" />
                        <flux:description>{{ __('Orders up to this weight will be charged the base rate.') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Base Rate') }} ({{ __('Currency') }})</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="baseRate" placeholder="0.00" />
                        <flux:error name="baseRate" />
                        <flux:description>{{ __('Shipping cost for orders within the base weight.') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Per KG Rate') }} ({{ __('Currency') }})</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="perKgRate" placeholder="0.00" />
                        <flux:error name="perKgRate" />
                        <flux:description>{{ __('Additional cost per KG beyond the base weight (rounded up).') }}</flux:description>
                    </flux:field>

                    @if ($baseRate > 0 && $perKgRate > 0)
                        <div class="mt-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">{{ __('Example Calculations:') }}</p>
                            <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• {{ number_format($baseWeightKg, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate, 2) }}</li>
                                <li>• {{ number_format($baseWeightKg + 1, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate + $perKgRate, 2) }}</li>
                                <li>• {{ number_format($baseWeightKg + 2, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate + ($perKgRate * 2), 2) }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- City + Weight Configuration --}}
            @if ($type === 'city')
                <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <flux:heading size="md" class="mb-2">{{ __('Default Rates') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mb-4">
                        {{ __('These rates will be used for cities without specific rates configured.') }}
                    </flux:text>

                    <flux:field>
                        <flux:label>{{ __('Base Weight') }} (KG)</flux:label>
                        <flux:input type="number" step="0.01" min="0.01" wire:model.live="baseWeightKg" placeholder="1.00" />
                        <flux:error name="baseWeightKg" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Base Rate') }} ({{ __('Currency') }})</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="baseRate" placeholder="0.00" />
                        <flux:error name="baseRate" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Per KG Rate') }} ({{ __('Currency') }})</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="perKgRate" placeholder="0.00" />
                        <flux:error name="perKgRate" />
                    </flux:field>

                    @if ($baseRate > 0 && $perKgRate > 0)
                        <div class="mt-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">{{ __('Default Rate Examples:') }}</p>
                            <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• {{ number_format($baseWeightKg, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate, 2) }}</li>
                                <li>• {{ number_format($baseWeightKg + 1, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate + $perKgRate, 2) }}</li>
                                <li>• {{ number_format($baseWeightKg + 2, 2) }} KG: {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($baseRate + ($perKgRate * 2), 2) }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <flux:field>
                <flux:switch wire:model="isActive" label="{{ __('Active') }}" description="{{ __('Enable this shipping configuration') }}" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        {{ __('Save Settings') }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </div>

    {{-- City Rates Management (Only shown when city mode is selected) --}}
    @if ($type === 'city')
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                        <flux:icon.map-pin class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <flux:heading size="md" level="3">{{ __('City-Specific Rates') }}</flux:heading>
                        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Configure shipping rates for specific cities or set a default rate for all other cities') }}
                        </flux:text>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if (!$restOfAllCitiesRate)
                        <flux:button wire:click="openRestOfAllCitiesModal" variant="ghost">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>{{ __('Set Rest of All Cities') }}</span>
                            </span>
                        </flux:button>
                    @endif
                    <flux:button wire:click="openCityRateModal" variant="primary">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>{{ __('Add City Rate') }}</span>
                        </span>
                    </flux:button>
                </div>
            </div>

            {{-- Search --}}
            <div class="mb-4">
                <flux:field>
                    <flux:input wire:model.live.debounce.300ms="searchCity" placeholder="{{ __('Search cities...') }}" />
                </flux:field>
            </div>

            {{-- City Rates Table --}}
            @if ($cityRates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('City') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Base Weight') }} (KG)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Base Rate') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Per KG Rate') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach ($cityRates as $cityRate)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if ($cityRate->isRestOfAllCities())
                                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                </svg>
                                                <div class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                    {{ __('Rest of All Cities') }}
                                                </div>
                                            @else
                                                <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                    {{ $cityRate->city->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ number_format($cityRate->base_weight_kg, 2) }} KG
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white font-semibold">
                                        {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($cityRate->base_rate, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white font-semibold">
                                        {{ \App\Models\Setting::get('currency_symbol', '৳') }}{{ number_format($cityRate->per_kg_rate, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($cityRate->is_active)
                                            <flux:badge variant="success" size="sm">{{ __('Active') }}</flux:badge>
                                        @else
                                            <flux:badge variant="danger" size="sm">{{ __('Inactive') }}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button wire:click="toggleCityRateStatus({{ $cityRate->id }})" variant="ghost" size="sm" wire:loading.attr="disabled" wire:target="toggleCityRateStatus({{ $cityRate->id }})">
                                                <span wire:loading.remove wire:target="toggleCityRateStatus({{ $cityRate->id }})">
                                                    {{ $cityRate->is_active ? __('Deactivate') : __('Activate') }}
                                                </span>
                                                <span wire:loading wire:target="toggleCityRateStatus({{ $cityRate->id }})">
                                                    ...
                                                </span>
                                            </flux:button>
                                            <flux:button wire:click="openCityRateModal({{ $cityRate->id }})" variant="ghost" size="sm">
                                                {{ __('Edit') }}
                                            </flux:button>
                                            <flux:button wire:click="deleteCityRate({{ $cityRate->id }})" wire:confirm="{{ __('Are you sure you want to delete this city rate?') }}" variant="danger" size="sm" wire:loading.attr="disabled" wire:target="deleteCityRate({{ $cityRate->id }})">
                                                <span wire:loading.remove wire:target="deleteCityRate({{ $cityRate->id }})">
                                                    {{ __('Delete') }}
                                                </span>
                                                <span wire:loading wire:target="deleteCityRate({{ $cityRate->id }})">
                                                    ...
                                                </span>
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <flux:callout variant="info">
                    {{ __('No city rates configured yet. Add your first city rate to get started.') }}
                </flux:callout>
            @endif
        </div>
    @endif

    {{-- City Rate Modal --}}
    @if($showCityRateModal)
        <flux:modal wire:model="showCityRateModal" name="city-rate-modal">
            <form wire:submit.prevent="saveCityRate" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    @if($isRestOfAllCities)
                        {{ $editingCityRateId ? __('Edit Rest of All Cities Rate') : __('Set Rest of All Cities Rate') }}
                    @else
                        {{ $editingCityRateId ? __('Edit City Rate') : __('Add City Rate') }}
                    @endif
                </flux:heading>
            </div>

            <div class="space-y-4">
                @if(!$editingCityRateId || ($editingCityRateId && !$isRestOfAllCities))
                    <flux:field>
                        <flux:checkbox wire:model.live="isRestOfAllCities" label="{{ __('Apply to Rest of All Cities') }}" description="{{ __('This rate will apply to all cities that don\'t have a specific rate configured') }}" />
                    </flux:field>
                @endif

                <flux:field>
                    <flux:label>{{ __('City') }}</flux:label>
                    @if($isRestOfAllCities)
                        <flux:input value="{{ __('Rest of All Cities') }}" disabled />
                        <flux:description>{{ __('This rate will be used for all cities without specific rates') }}</flux:description>
                    @elseif($editingCityRateId)
                        <flux:select wire:model="selectedCityId" disabled>
                            <option value="">{{ __('Select a city') }}</option>
                            @php
                                $editingCity = \App\Models\City::find($selectedCityId);
                            @endphp
                            @if ($editingCity)
                                <option value="{{ $editingCity->id }}" selected>{{ $editingCity->name }}</option>
                            @endif
                        </flux:select>
                        <flux:description>{{ __('City cannot be changed when editing') }}</flux:description>
                    @else
                        <flux:select wire:model="selectedCityId">
                            <option value="">{{ __('Select a city') }}</option>
                            @foreach ($availableCities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>{{ __('Select a specific city or check "Rest of All Cities" above') }}</flux:description>
                    @endif
                    <flux:error name="selectedCityId" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Base Weight') }} (KG)</flux:label>
                    <flux:input type="number" step="0.01" min="0.01" wire:model="cityBaseWeightKg" placeholder="1.00" />
                    <flux:error name="cityBaseWeightKg" />
                    <flux:description>{{ __('Orders up to this weight will be charged the base rate.') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Base Rate') }} ({{ __('Currency') }})</flux:label>
                    <flux:input type="number" step="0.01" min="0" wire:model="cityBaseRate" placeholder="0.00" />
                    <flux:error name="cityBaseRate" />
                    <flux:description>{{ __('Shipping cost for orders within the base weight.') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Per KG Rate') }} ({{ __('Currency') }})</flux:label>
                    <flux:input type="number" step="0.01" min="0" wire:model="cityPerKgRate" placeholder="0.00" />
                    <flux:error name="cityPerKgRate" />
                    <flux:description>{{ __('Additional cost per KG beyond the base weight (rounded up).') }}</flux:description>
                </flux:field>

                <flux:field>
                    <flux:switch wire:model="cityIsActive" label="{{ __('Active') }}" description="{{ __('Enable this city rate') }}" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="button" wire:click="closeCityRateModal" variant="ghost" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveCityRate">
                        {{ __('Save') }}
                    </span>
                    <span wire:loading wire:target="saveCityRate">
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
        </flux:modal>
    @endif
</div>
