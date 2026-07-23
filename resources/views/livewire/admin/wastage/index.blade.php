<div class="space-y-6">
    <x-admin.page-header :heading="__('Wastage')" :description="__('Record damaged, expired, or stolen stock and route it through manager approval before it leaves inventory')">
        @can('create wastage')
            <flux:button wire:click="openReportModal" variant="primary">
                {{ __('Report Wastage') }}
            </flux:button>
        @endcan
    </x-admin.page-header>

    @if (session()->has('message'))
        <flux:callout variant="success">{{ session('message') }}</flux:callout>
    @endif

    @if (session()->has('error'))
        <flux:callout variant="danger">{{ session('error') }}</flux:callout>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat-card :label="__('Pending Approvals')" :value="$stats['pending']" tone="amber">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Approved Qty This Month')" :value="number_format($stats['approved_qty_month'])" tone="red">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Rejected This Month')" :value="$stats['rejected_month']" tone="emerald">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>

        <x-admin.stat-card :label="__('Wastage Value This Month')" :value="\App\Models\Setting::get('currency_symbol', '৳').number_format($stats['value_month'], 2)" tone="indigo">
            <x-slot:icon>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot:icon>
        </x-admin.stat-card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <flux:field>
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by product name or SKU...') }}" />
            </flux:field>
        </div>
        <flux:field>
            <flux:select wire:model.live="filterStatus">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="approved">{{ __('Approved') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:select wire:model.live="filterReason">
                <option value="">{{ __('All Reasons') }}</option>
                @foreach(\App\Enums\WastageReason::cases() as $reason)
                    <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        @if($activeWarehouses->count() > 1)
            <flux:field>
                <flux:select wire:model.live="filterWarehouse">
                    <option value="">{{ __('All Warehouses') }}</option>
                    @foreach($activeWarehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>
        @endif
        <flux:field>
            <flux:select wire:model.live="perPage">
                <option value="10">10 {{ __('per page') }}</option>
                <option value="15">15 {{ __('per page') }}</option>
                <option value="25">25 {{ __('per page') }}</option>
                <option value="50">50 {{ __('per page') }}</option>
            </flux:select>
        </flux:field>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-zinc-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Product') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Qty') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Reason') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Reported By') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($logs as $log)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $log->product?->name_en }}</div>
                            @if($log->productAttribute)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $log->productAttribute->attribute_label }}</div>
                            @endif
                            @if($log->photo_path)
                                <a href="{{ asset('storage/'.$log->photo_path) }}" target="_blank" rel="noopener" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ __('View photo') }}</a>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $log->warehouse?->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">{{ $log->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge size="sm" :variant="$log->reason->badgeColor()">{{ $log->reason->label() }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $log->reportedBy?->name ?? __('System') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:badge size="sm" :variant="match($log->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                                {{ ucfirst($log->status) }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @can('approve wastage')
                                <flux:button wire:click="openReviewModal({{ $log->id }})" size="sm" variant="ghost">
                                    {{ $log->status === 'pending' ? __('Review') : __('View') }}
                                </flux:button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty-state :colspan="8" :title="__('No wastage reported yet')" :description="__('Reports you file will appear here awaiting manager approval.')">
                        <x-slot:icon>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </x-slot:icon>
                    </x-admin.table-empty-state>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif

    {{-- Report Wastage Modal --}}
    @if($showReportModal)
        <flux:modal wire:model="showReportModal" name="report-wastage-modal" class="max-w-xl">
            <form wire:submit.prevent="saveReport" class="space-y-6">
                <div>
                    <flux:heading>{{ __('Report Wastage') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-0.5">{{ __('This will be sent for manager approval before stock is deducted.') }}</flux:text>
                </div>

                <flux:field>
                    <flux:label>{{ __('Product') }}</flux:label>
                    @if($reportProduct)
                        <div class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                            <div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $reportProduct->name_en }}</div>
                                @if($reportProduct->sku)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">SKU: {{ $reportProduct->sku }}</div>
                                @endif
                            </div>
                            <flux:button type="button" wire:click="clearReportProduct" size="sm" variant="ghost">{{ __('Change') }}</flux:button>
                        </div>
                    @else
                        <flux:input wire:model.live.debounce.300ms="productSearch" placeholder="{{ __('Search by name or SKU...') }}" />
                        @if($productSearch !== '')
                            <div class="mt-2 max-h-48 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                                @forelse($reportableProducts as $product)
                                    <button type="button" wire:click="selectReportProduct({{ $product->id }})" class="w-full text-left px-3 py-2 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800 last:border-b-0">
                                        <span class="font-medium text-zinc-900 dark:text-white">{{ $product->name_en }}</span>
                                        @if($product->sku)
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400 ml-2">SKU: {{ $product->sku }}</span>
                                        @endif
                                    </button>
                                @empty
                                    <div class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No matching products.') }}</div>
                                @endforelse
                            </div>
                        @endif
                    @endif
                    <flux:error name="reportProductId" />
                </flux:field>

                @if($reportProduct)
                    <div class="grid grid-cols-2 gap-4">
                        @if($activeWarehouses->count() > 1)
                            <flux:field>
                                <flux:label>{{ __('Warehouse') }}</flux:label>
                                <flux:select wire:model.live="reportWarehouseId">
                                    @foreach($activeWarehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        @endif

                        @if($reportProduct->hasAttributes())
                            <flux:field>
                                <flux:label>{{ __('Variant') }}</flux:label>
                                <flux:select wire:model="reportProductAttributeId">
                                    <option value="">{{ __('Select variant') }}</option>
                                    @foreach($reportProduct->productAttributes as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->attribute_label }}</option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        @endif
                    </div>

                    @if($reportProduct->tracks_batches)
                        <flux:field>
                            <flux:label>{{ __('Batch') }}</flux:label>
                            <flux:select wire:model="reportProductBatchId">
                                <option value="">{{ __('Select batch') }}</option>
                                @foreach($reportBatches as $batch)
                                    <option value="{{ $batch->id }}">
                                        {{ $batch->batch_number }} — {{ __(':qty in stock', ['qty' => $batch->quantity]) }}
                                        @if($batch->expires_at) ({{ __('expires :date', ['date' => $batch->expires_at->format('M d, Y')]) }}) @endif
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:error name="reportProductBatchId" />
                        </flux:field>
                    @endif

                    <flux:field>
                        <flux:label>{{ __('Quantity') }}</flux:label>
                        <flux:input type="number" min="1" wire:model="reportQuantity" />
                        <flux:error name="reportQuantity" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Reason') }}</flux:label>
                        <flux:select wire:model="reportReason">
                            @foreach(\App\Enums\WastageReason::cases() as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Notes') }}</flux:label>
                        <flux:textarea wire:model="reportNotes" rows="2" placeholder="{{ __('Any additional detail...') }}" />
                        <flux:error name="reportNotes" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Photo evidence (optional)') }}</flux:label>
                        <x-media.drag-drop-uploader
                            wire-model="photo"
                            :value="$photo"
                            :label="__('Upload photo')"
                            accept="image/*"
                        />
                        <flux:error name="photo" />
                    </flux:field>
                @endif

                <div class="flex justify-end gap-3">
                    <flux:button type="button" wire:click="closeReportModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                    @if($reportProduct)
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveReport">
                            {{ __('Submit for Approval') }}
                        </flux:button>
                    @endif
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- Review Modal --}}
    @if($showReviewModal && $reviewingLog)
        <flux:modal wire:model="showReviewModal" name="review-wastage-modal" class="max-w-xl">
            <div class="space-y-6">
                <div>
                    <flux:heading>{{ __('Review Wastage Report') }}</flux:heading>
                    <flux:text size="sm" variant="subtle" class="mt-0.5">{{ $reviewingLog->product?->name_en }}</flux:text>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Variant') }}</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $reviewingLog->productAttribute?->attribute_label ?? __('Base product') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Warehouse') }}</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $reviewingLog->warehouse?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Quantity') }}</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $reviewingLog->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Reason') }}</dt>
                        <dd><flux:badge size="sm" :variant="$reviewingLog->reason->badgeColor()">{{ $reviewingLog->reason->label() }}</flux:badge></dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Reported By') }}</dt>
                        <dd class="font-medium text-zinc-900 dark:text-white">{{ $reviewingLog->reportedBy?->name ?? __('System') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</dt>
                        <dd>
                            <flux:badge size="sm" :variant="match($reviewingLog->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                                {{ ucfirst($reviewingLog->status) }}
                            </flux:badge>
                        </dd>
                    </div>
                </dl>

                @if($reviewingLog->notes)
                    <div>
                        <flux:text size="sm" variant="subtle">{{ __('Notes') }}</flux:text>
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $reviewingLog->notes }}</p>
                    </div>
                @endif

                @if($reviewingLog->photo_path)
                    <div>
                        <flux:text size="sm" variant="subtle" class="mb-2 block">{{ __('Photo evidence') }}</flux:text>
                        <img src="{{ asset('storage/'.$reviewingLog->photo_path) }}" class="max-h-64 rounded-lg border border-zinc-200 dark:border-zinc-700" alt="{{ __('Wastage photo evidence') }}">
                    </div>
                @endif

                @if($reviewingLog->canBeReviewed())
                    <flux:field>
                        <flux:label>{{ __('Review notes (optional)') }}</flux:label>
                        <flux:textarea wire:model="reviewNotes" rows="2" />
                    </flux:field>

                    <div class="flex justify-end gap-3">
                        <flux:button type="button" wire:click="closeReviewModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                        <flux:button type="button" wire:click="reject" variant="danger" wire:loading.attr="disabled" wire:target="reject">
                            {{ __('Reject') }}
                        </flux:button>
                        <flux:button type="button" wire:click="approve" variant="primary" wire:loading.attr="disabled" wire:target="approve">
                            {{ __('Approve & Deduct Stock') }}
                        </flux:button>
                    </div>
                @else
                    @if($reviewingLog->review_notes)
                        <div>
                            <flux:text size="sm" variant="subtle">{{ __('Review notes') }}</flux:text>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $reviewingLog->review_notes }}</p>
                        </div>
                    @endif
                    <div class="flex justify-end">
                        <flux:button type="button" wire:click="closeReviewModal" variant="ghost">{{ __('Close') }}</flux:button>
                    </div>
                @endif
            </div>
        </flux:modal>
    @endif
</div>
