<div class="space-y-6 max-w-4xl">
    <x-admin.page-header :heading="__('New Stock Transfer')" :description="__('Move stock from one warehouse to another')">
        <flux:button :href="route('admin.stock-transfers.index')" variant="ghost" wire:navigate>
            {{ __('Back to Transfers') }}
        </flux:button>
    </x-admin.page-header>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('From Warehouse') }}</flux:label>
                    <flux:select wire:model="from_warehouse_id">
                        <option value="">{{ __('Select warehouse') }}</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="from_warehouse_id" />
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('To Warehouse') }}</flux:label>
                    <flux:select wire:model="to_warehouse_id">
                        <option value="">{{ __('Select warehouse') }}</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="to_warehouse_id" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="2" placeholder="{{ __('Optional notes about this transfer...') }}" />
                <flux:error name="notes" />
            </flux:field>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6 space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">{{ __('Items') }}</flux:heading>
                <flux:button type="button" wire:click="addItem" size="sm" variant="ghost">
                    {{ __('Add Item') }}
                </flux:button>
            </div>

            @foreach($items as $index => $item)
                <div wire:key="item-{{ $index }}" class="grid grid-cols-12 gap-3 items-start rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                    <div class="col-span-5">
                        <flux:field>
                            <flux:label>{{ __('Product') }}</flux:label>
                            <flux:select wire:model="items.{{ $index }}.product_id">
                                <option value="">{{ __('Select product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name_en }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="items.{{ $index }}.product_id" />
                        </flux:field>
                    </div>

                    @php $variants = $this->attributesForProduct($item['product_id'] ?? null); @endphp
                    @if($variants->isNotEmpty())
                        <div class="col-span-4">
                            <flux:field>
                                <flux:label>{{ __('Variant') }}</flux:label>
                                <flux:select wire:model="items.{{ $index }}.product_attribute_id">
                                    <option value="">{{ __('Select variant') }}</option>
                                    @foreach($variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->attribute_label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="items.{{ $index }}.product_attribute_id" />
                            </flux:field>
                        </div>
                        <div class="col-span-2">
                    @else
                        <div class="col-span-6">
                    @endif
                            <flux:field>
                                <flux:label>{{ __('Quantity') }}</flux:label>
                                <flux:input type="number" min="1" wire:model="items.{{ $index }}.quantity" />
                                <flux:error name="items.{{ $index }}.quantity" />
                            </flux:field>
                        </div>

                    <div class="col-span-1 flex items-end h-full pb-1">
                        @if(count($items) > 1)
                            <flux:button type="button" wire:click="removeItem({{ $index }})" size="sm" variant="danger">
                                &times;
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endforeach

            @error('items') <flux:error name="items" /> @enderror
        </div>

        <div class="flex justify-end gap-3">
            <flux:button :href="route('admin.stock-transfers.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                {{ __('Create Transfer') }}
            </flux:button>
        </div>
    </form>
</div>
