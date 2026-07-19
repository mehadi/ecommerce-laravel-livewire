{{--
    Shared searchable city combobox (Alpine.js), reused by every checkout
    variant so this stateful client-side logic only lives in one place.

    Optional: $dense (bool) — smaller trigger button for compact layouts.
--}}
@php
    $dense = $dense ?? false;
    $triggerClass = $dense
        ? 'w-full min-h-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl px-4 py-2 text-sm font-medium text-left flex items-center justify-between gap-2 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-600 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200'
        : 'w-full min-h-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 text-[15px] font-medium text-left flex items-center justify-between gap-2 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-600 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200';
@endphp
<div
    x-data="{
        open: false,
        search: '',
        selectedCity: null,
        cities: @js($this->cities->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray()),
        get filteredCities() {
            if (!this.search) return this.cities;
            const query = this.search.toLowerCase();
            return this.cities.filter(city =>
                city.name.toLowerCase().includes(query)
            );
        },
        selectCity(city) {
            this.selectedCity = city;
            $wire.set('shippingCityId', city.id);
            this.open = false;
            this.search = '';
            // Trigger shipping cost recalculation
            $wire.$refresh();
        },
        init() {
            const selected = this.cities.find(c => c.id === $wire.shippingCityId);
            if (selected) this.selectedCity = selected;
            $watch('$wire.shippingCityId', value => {
                const city = this.cities.find(c => c.id === value);
                if (city) this.selectedCity = city;
            });
        }
    }"
    class="relative"
    @click.away="open = false"
>
    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
        {{ __('City') }} <span class="text-red-500">*</span>
    </label>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open ? 'true' : 'false'"
        class="{{ $triggerClass }}"
        :class="selectedCity ? 'border-emerald-400 dark:border-emerald-500' : ''"
    >
        <span class="truncate" :class="selectedCity ? 'text-zinc-900 dark:text-white' : 'text-zinc-400 dark:text-zinc-500'">
            <span x-text="selectedCity ? selectedCity.name : '{{ __('Select City') }}'"></span>
        </span>
        <svg class="w-4 h-4 text-zinc-400 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute z-50 w-full mt-2 bg-white dark:bg-zinc-800 rounded-2xl shadow-[0_16px_40px_-12px_rgb(16_24_40_/_0.18)] ring-1 ring-zinc-900/[0.04] dark:ring-white/[0.06] max-h-64 overflow-hidden"
        style="display: none;"
    >
        <!-- Search Input -->
        <div class="p-2.5 border-b border-zinc-900/[0.06] dark:border-white/[0.08] sticky top-0 bg-white dark:bg-zinc-800 z-10">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    x-model="search"
                    @click.stop
                    @keydown.escape="open = false"
                    class="w-full pl-9 pr-5 py-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-full text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 transition-shadow duration-200"
                    placeholder="{{ __('Search cities...') }}"
                >
            </div>
        </div>

        <!-- Options List -->
        <div class="overflow-y-auto max-h-48 custom-scrollbar p-1">
            <template x-if="filteredCities.length === 0">
                <div class="p-4 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('No cities found') }}
                </div>
            </template>
            <template x-for="city in filteredCities" :key="city.id">
                <button
                    type="button"
                    @click="selectCity(city)"
                    class="w-full px-3.5 py-2.5 text-left text-sm rounded-xl cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors duration-150 flex items-center gap-2"
                    :class="selectedCity && selectedCity.id === city.id ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-zinc-700 dark:text-zinc-300'"
                >
                    <svg x-show="selectedCity && selectedCity.id === city.id" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span x-text="city.name" class="flex-1"></span>
                </button>
            </template>
        </div>
    </div>

    @error('shippingCityId')
        <span class="text-red-600 dark:text-red-400 text-sm mt-1.5 flex items-center gap-1.5" role="alert">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
        </span>
    @enderror
</div>
