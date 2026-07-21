<div class="space-y-6 max-w-4xl">
    <x-admin.page-header :heading="__('New Purchase Order')" :description="__('Order stock from a supplier')">
        <flux:button :href="route('admin.purchase-orders.index')" variant="ghost" wire:navigate>
            {{ __('Back to Purchase Orders') }}
        </flux:button>
    </x-admin.page-header>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Supplier') }}</flux:label>
                    <flux:select wire:model="supplier_id">
                        <option value="">{{ __('Select supplier') }}</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="supplier_id" />
                    @if($suppliers->isEmpty())
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                            {{ __('No active suppliers yet.') }}
                            <a href="{{ route('admin.suppliers.index') }}" wire:navigate class="underline">{{ __('Add one first') }}</a>.
                        </p>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('Required') }}">{{ __('Receiving Warehouse') }}</flux:label>
                    <flux:select wire:model="warehouse_id">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="warehouse_id" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Expected Delivery Date') }}</flux:label>
                <flux:input type="date" wire:model="expected_at" />
                <flux:error name="expected_at" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Notes') }}</flux:label>
                <flux:textarea wire:model="notes" rows="2" placeholder="{{ __('Optional notes about this order...') }}" />
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
                    <div class="col-span-4">
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
                        <div class="col-span-3">
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
                        <div class="col-span-5">
                    @endif
                            <flux:field>
                                <flux:label>{{ __('Quantity') }}</flux:label>
                                <flux:input type="number" min="1" wire:model="items.{{ $index }}.quantity_ordered" />
                                <flux:error name="items.{{ $index }}.quantity_ordered" />
                            </flux:field>
                        </div>

                    <div class="col-span-2">
                        <flux:field>
                            <flux:label>{{ __('Unit Cost') }}</flux:label>
                            <flux:input type="number" step="0.01" min="0" wire:model="items.{{ $index }}.unit_cost" placeholder="0.00" />
                            <flux:error name="items.{{ $index }}.unit_cost" />
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
            <flux:button :href="route('admin.purchase-orders.index')" variant="ghost" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                {{ __('Create Purchase Order') }}
            </flux:button>
        </div>
    </form>
</div>
