<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosCashMovement;
use App\Models\PosHeldSale;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use App\Notifications\PosShiftClosedWithVariance;
use App\Services\PosSaleService;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use RuntimeException;

/**
 * The till screen. One register per tenant is auto-provisioned on first use
 * (PosRegister::default()) so a tenant can start selling immediately without
 * first visiting the (admin) register management screen.
 *
 * Cart/customer/payment state lives only in the Livewire component's memory
 * for the duration of the page — it is not session- or DB-persisted until
 * either "Hold Sale" (pos_held_sales) or a completed checkout (a real Order).
 * A page refresh mid-sale loses an unheld cart; this mirrors how the
 * storefront's own cart works today and is a reasonable v1 tradeoff.
 */
class Terminal extends Component
{
    public int $registerId;

    public ?int $shiftId = null;

    // Open-shift form
    public float $openingCash = 0;

    // Close-shift form
    public bool $showCloseShiftForm = false;

    public float $closingCash = 0;

    public string $closeNotes = '';

    // Cart: keyed by "product_{id}" or "attr_{id}" so re-adding the same line increments quantity.
    public array $cart = [];

    // Product search / browse
    public string $search = '';

    public ?int $categoryFilter = null;

    public string $categorySearch = '';

    public string $viewMode = 'grid'; // 'grid'|'list'

    // How many browse/search results to show — a simple "load more" counter
    // rather than real cursor pagination, since the product catalog is
    // realistically in the hundreds/low-thousands for a single-store tenant.
    public int $productLimit = 24;

    public ?int $variantPickerProductId = null;

    public ?int $viewProductId = null;

    // Customer
    public string $customerPhone = '';

    public string $customerName = '';

    public ?int $selectedCustomerId = null;

    public bool $showCustomerForm = false;

    // Discount / coupon / notes
    public float $discountAmount = 0;

    public string $couponCode = '';

    public ?int $appliedCouponId = null;

    public string $notes = '';

    // Payments
    public array $payments = [];

    public string $paymentMethod = 'cash';

    public float $paymentTendered = 0;

    public string $paymentReference = '';

    // Hold
    public string $holdNote = '';

    // Undo-remove: the most recently removed cart line, restorable once.
    public ?array $lastRemovedLine = null;

    // Receipt
    public ?int $completedOrderId = null;

    // Survives past startNewSale() (unlike completedOrderId) so "reprint" stays
    // available for the last sale even after the cashier has moved on.
    public ?int $lastCompletedOrderId = null;

    public ?int $reprintOrderId = null;

    public function mount(): void
    {
        Gate::authorize('access pos');

        $register = PosRegister::default();
        $this->registerId = $register->id;
        $this->shiftId = $register->openShift()?->id;
    }

    #[Computed]
    public function register(): PosRegister
    {
        return PosRegister::with('warehouse')->findOrFail($this->registerId);
    }

    #[Computed]
    public function shift(): ?PosShift
    {
        return $this->shiftId ? PosShift::find($this->shiftId) : null;
    }

    #[Computed]
    public function completedOrder(): ?Order
    {
        return $this->completedOrderId
            ? Order::with(['items', 'payments', 'customer'])->find($this->completedOrderId)
            : null;
    }

    #[Computed]
    public function reprintOrder(): ?Order
    {
        return $this->reprintOrderId
            ? Order::with(['items', 'payments', 'customer'])->find($this->reprintOrderId)
            : null;
    }

    /**
     * Last 8 distinct products actually sold through this POS channel,
     * newest first — pure read of existing OrderItem history, no new schema.
     * Pulls a small buffer (30 rows) before de-duplicating in memory rather
     * than a DISTINCT-with-ORDER-BY query, which several DB engines don't
     * guarantee to combine predictably.
     */
    #[Computed]
    public function recentlySold()
    {
        $productIds = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.channel', 'pos')
            ->whereNull('order_items.product_attribute_id')
            ->orderByDesc('order_items.id')
            ->limit(30)
            ->pluck('order_items.product_id')
            ->unique()
            ->take(8)
            ->values();

        if ($productIds->isEmpty()) {
            return collect();
        }

        return Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($product) => $productIds->search($product->id))
            ->values();
    }

    /**
     * Shared where-clauses for both the paginated result set and the total
     * count check below — kept in one place so "how many results exist" and
     * "which page of results" can never drift apart.
     */
    private function productQuery()
    {
        if (mb_strlen($this->search) >= 1) {
            return Product::query()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('name_en', 'like', '%'.$this->search.'%')
                        ->orWhere('name_bn', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%')
                        ->orWhere('barcode', 'like', '%'.$this->search.'%');
                })
                ->orderBy('name_en');
        }

        return Product::query()
            ->where('is_active', true)
            ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->orderBy('name_en');
    }

    /**
     * With no search text, this is the "browse" grid/list (optionally
     * narrowed by a category chip) so a cashier isn't forced to type before
     * anything is tappable. Typing a search term switches to a name/SKU/
     * barcode match across the whole catalog and takes priority over the
     * category filter.
     */
    #[Computed]
    public function searchResults()
    {
        return $this->productQuery()->with('productAttributes')->limit($this->productLimit)->get();
    }

    #[Computed]
    public function hasMoreProducts(): bool
    {
        return $this->productQuery()->count() > $this->productLimit;
    }

    public function loadMoreProducts(): void
    {
        $this->productLimit += 24;
    }

    public function updatedSearch(): void
    {
        $this->productLimit = 24;
    }

    #[Computed]
    public function categories()
    {
        return Category::where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->when($this->categorySearch !== '', fn ($query) => $query->where(function ($query) {
                $query->where('name_en', 'like', '%'.$this->categorySearch.'%')
                    ->orWhere('name_bn', 'like', '%'.$this->categorySearch.'%');
            }))
            ->orderBy('name_en')
            ->get();
    }

    /**
     * Ignores $categorySearch (unlike categories()) so the category rail —
     * search box included — stays visible while a query matches nothing,
     * rather than disappearing along with its own input.
     */
    #[Computed]
    public function hasAnyCategories(): bool
    {
        return Category::where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    /**
     * One grouped query for every category's product count, rather than a
     * per-chip count query (would be an N+1 across the category rail).
     */
    #[Computed]
    public function categoryCounts(): array
    {
        return Product::where('is_active', true)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, count(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id')
            ->all();
    }

    #[Computed]
    public function totalActiveProductsCount(): int
    {
        return Product::where('is_active', true)->count();
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->categoryFilter = $this->categoryFilter === $categoryId ? null : $categoryId;
        $this->productLimit = 24;
    }

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    #[Computed]
    public function variantPickerProduct(): ?Product
    {
        return $this->variantPickerProductId
            ? Product::with('productAttributes')->find($this->variantPickerProductId)
            : null;
    }

    #[Computed]
    public function viewProduct(): ?Product
    {
        return $this->viewProductId
            ? Product::with(['category', 'productAttributes'])->find($this->viewProductId)
            : null;
    }

    public function viewProductDetails(int $productId): void
    {
        $this->viewProductId = $productId;
    }

    public function closeProductDetails(): void
    {
        $this->viewProductId = null;
    }

    /**
     * Same as addProductToCart(), but dismisses the details modal first —
     * used by the modal's own "Add to Cart" button. If the product has
     * variants, addProductToCart() will still open the variant picker.
     */
    public function addProductFromDetails(int $productId): void
    {
        $this->viewProductId = null;
        $this->addProductToCart($productId);
    }

    #[Computed]
    public function heldSales()
    {
        return PosHeldSale::where('register_id', $this->registerId)
            ->orderByDesc('held_at')
            ->get();
    }

    #[Computed]
    public function selectedCustomer(): ?Customer
    {
        return $this->selectedCustomerId ? Customer::find($this->selectedCustomerId) : null;
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cart)->sum(fn ($line) => $line['unit_price'] * $line['quantity']);
    }

    #[Computed]
    public function couponDiscount(): float
    {
        if (! $this->appliedCouponId) {
            return 0;
        }

        $coupon = Coupon::find($this->appliedCouponId);

        return $coupon ? $coupon->calculateDiscount($this->subtotal()) : 0;
    }

    #[Computed]
    public function totalDiscount(): float
    {
        return round($this->discountAmount + $this->couponDiscount(), 2);
    }

    #[Computed]
    public function total(): float
    {
        return max(0, round($this->subtotal() - $this->totalDiscount(), 2));
    }

    #[Computed]
    public function paidSoFar(): float
    {
        return round(collect($this->payments)->sum('amount'), 2);
    }

    #[Computed]
    public function remainingDue(): float
    {
        return max(0, round($this->total() - $this->paidSoFar(), 2));
    }

    /**
     * Suggested one-tap cash amounts: the exact balance plus a few round-up
     * denominations (common banknotes), so a cashier rarely has to type.
     */
    #[Computed]
    public function quickCashOptions(): array
    {
        $due = $this->remainingDue();

        if ($due <= 0) {
            return [];
        }

        $options = [$due];

        foreach ([50, 100, 500, 1000] as $denomination) {
            $rounded = (float) (ceil($due / $denomination) * $denomination);

            if ($rounded > $due && ! in_array($rounded, $options, true)) {
                $options[] = $rounded;
            }
        }

        sort($options);

        return array_slice($options, 0, 4);
    }

    #[Computed]
    public function expectedCash(): float
    {
        $shift = $this->shift();

        if (! $shift) {
            return 0;
        }

        $movements = $shift->cashMovements()->get();

        return round(
            $shift->opening_cash
            + $movements->whereIn('type', ['cash_in', 'sale_cash'])->sum('amount')
            - $movements->whereIn('type', ['cash_out', 'refund_cash'])->sum('amount'),
            2
        );
    }

    public function openShift(): void
    {
        Gate::authorize('open pos shift');

        $register = $this->register();

        if ($register->openShift()) {
            session()->flash('error', __('This register already has an open shift.'));

            return;
        }

        $this->validate(['openingCash' => 'required|numeric|min:0']);

        $shift = PosShift::create([
            'register_id' => $register->id,
            'opened_by' => Auth::id(),
            'opening_cash' => $this->openingCash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->shiftId = $shift->id;
        unset($this->register, $this->shift);
        session()->flash('message', __('Shift opened.'));
    }

    public function confirmCloseShift(): void
    {
        Gate::authorize('close pos shift');
        $this->closingCash = $this->expectedCash();
        $this->showCloseShiftForm = true;
    }

    public function closeShift(): void
    {
        Gate::authorize('close pos shift');

        $shift = $this->shift();

        if (! $shift) {
            return;
        }

        $this->validate(['closingCash' => 'required|numeric|min:0']);

        $expected = $this->expectedCash();
        $variance = round($this->closingCash - $expected, 2);

        $shift->update([
            'closed_by' => Auth::id(),
            'closing_cash' => $this->closingCash,
            'expected_cash' => $expected,
            'variance' => $variance,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $this->closeNotes ?: null,
        ]);

        if (abs($variance) > 0.009 && Tenancy::current()) {
            // A plain whereHas() rather than Spatie's role() scope, which throws
            // RoleDoesNotExist if any named role hasn't been created yet for this
            // tenant — this notification shouldn't ever block closing a shift.
            $recipients = User::whereHas('roles', fn ($query) => $query->whereIn('name', ['super admin', 'admin', 'manager']))->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new PosShiftClosedWithVariance($shift, Tenancy::current()));
            }
        }

        $this->shiftId = null;
        $this->showCloseShiftForm = false;
        unset($this->register, $this->shift);
        session()->flash('message', __('Shift closed. Variance: :variance', ['variance' => number_format($variance, 2)]));
    }

    public function addProductToCart(int $productId): void
    {
        $product = Product::with('productAttributes')->findOrFail($productId);

        if ($product->hasAttributes()) {
            $this->variantPickerProductId = $productId;
            $this->search = '';

            return;
        }

        $key = 'product_'.$product->id;
        $currentQty = $this->cart[$key]['quantity'] ?? 0;
        $stock = $product->getSyncedStock();

        // A definitive product/SKU match (grid tap, list tap, or a scanned
        // barcode/SKU via scanBarcode()) is only ever a friendly early warning
        // here — the authoritative, lock-protected check still happens in
        // PosSaleService at checkout — but it catches the common case (empty
        // or already-fully-carted stock) before the cashier gets all the way
        // to payment.
        if ($currentQty + 1 > $stock) {
            session()->flash('error', $stock <= 0
                ? __('":name" is out of stock.', ['name' => $product->name_en])
                : __('Only :count of ":name" available.', ['count' => $stock, 'name' => $product->name_en]));
            $this->search = '';

            return;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
        } else {
            $this->cart[$key] = [
                'product_id' => $product->id,
                'product_attribute_id' => null,
                'product_name' => $product->name_en,
                'attribute_data' => null,
                'quantity' => 1,
                'unit_price' => $product->getSyncedPrice(),
            ];
        }

        $this->search = '';
        // Lets the scan/search input reclaim focus after the DOM updates, so a
        // cashier can keep scanning items back-to-back without touching the screen.
        $this->dispatch('pos-item-added');
    }

    public function addVariantToCart(int $productAttributeId): void
    {
        $variant = ProductAttribute::with('product')->findOrFail($productAttributeId);
        $key = 'attr_'.$variant->id;
        $currentQty = $this->cart[$key]['quantity'] ?? 0;

        if ($currentQty + 1 > $variant->stock) {
            session()->flash('error', $variant->stock <= 0
                ? __('":name" is out of stock.', ['name' => $variant->product->name_en])
                : __('Only :count of ":name" available.', ['count' => $variant->stock, 'name' => $variant->product->name_en]));

            // Keep the variant picker open (rather than clearing it) so the
            // cashier can pick a different variant of the same product that
            // might still have stock.
            return;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
        } else {
            $this->cart[$key] = [
                'product_id' => $variant->product_id,
                'product_attribute_id' => $variant->id,
                'product_name' => $variant->product->name_en,
                'attribute_data' => $variant->attribute_data,
                'quantity' => 1,
                'unit_price' => (float) $variant->price,
            ];
        }

        $this->variantPickerProductId = null;
        $this->search = '';
        $this->dispatch('pos-item-added');
    }

    /**
     * Enter-to-scan: an exact barcode or SKU match adds straight to the cart
     * (no click needed) — this is what makes a USB/keyboard-wedge scanner
     * work. Stock availability is enforced by addProductToCart()/
     * addVariantToCart() themselves, so a match against an out-of-stock item
     * flashes a notification instead of silently adding a line that would
     * only fail later at checkout.
     *
     * $code comes straight from the input's live DOM value at keydown time
     * (see terminal.blade.php's wire:keydown.enter), not the debounced
     * `search` model — a scanner types then sends Enter fast enough that the
     * 200ms debounce often hadn't synced `search` yet, silently no-op'ing
     * the scan. Falls back to `search` only for direct/test callers that
     * don't pass one.
     */
    public function scanBarcode(?string $code = null): void
    {
        $code = trim($code ?? $this->search);

        if ($code === '') {
            return;
        }

        $variant = ProductAttribute::where('barcode', $code)->first();

        if ($variant) {
            $this->addVariantToCart($variant->id);

            return;
        }

        $product = Product::where('barcode', $code)->orWhere('sku', $code)->first();

        if ($product) {
            $this->addProductToCart($product->id);
        }
    }

    public function updateCartQuantity(string $key, int $quantity): void
    {
        if (! isset($this->cart[$key])) {
            return;
        }

        if ($quantity <= 0) {
            $this->removeCartLine($key);

            return;
        }

        $this->cart[$key]['quantity'] = $quantity;
    }

    public function incrementCartQuantity(string $key): void
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
        }
    }

    public function decrementCartQuantity(string $key): void
    {
        $this->updateCartQuantity($key, ($this->cart[$key]['quantity'] ?? 1) - 1);
    }

    public function removeCartLine(string $key): void
    {
        Gate::authorize('void pos sale line');

        if (isset($this->cart[$key])) {
            $this->lastRemovedLine = ['key' => $key, 'line' => $this->cart[$key]];
        }

        unset($this->cart[$key]);
    }

    public function undoRemoveLine(): void
    {
        if ($this->lastRemovedLine) {
            $this->cart[$this->lastRemovedLine['key']] = $this->lastRemovedLine['line'];
            $this->lastRemovedLine = null;
        }
    }

    /**
     * Clears the whole in-progress sale before anything has been charged —
     * nothing is persisted yet at this point, so there's no order/payment to
     * reverse. Gated the same as a single-line void since it's the same
     * "discard line items" action, just applied to everything at once.
     */
    public function voidTransaction(): void
    {
        Gate::authorize('void pos sale line');

        $this->reset([
            'cart', 'selectedCustomerId', 'customerPhone', 'customerName', 'showCustomerForm',
            'discountAmount', 'couponCode', 'appliedCouponId', 'notes',
            'payments', 'paymentTendered', 'paymentReference', 'lastRemovedLine',
        ]);
    }

    public function findCustomer(): void
    {
        $this->validate(['customerPhone' => 'required|string|max:255']);

        $customer = Customer::where('phone', $this->customerPhone)->first();

        if ($customer) {
            $this->selectedCustomerId = $customer->id;
            $this->showCustomerForm = false;
        } else {
            $this->showCustomerForm = true;
        }
    }

    public function createCustomer(): void
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name' => $this->customerName,
            'phone' => $this->customerPhone ?: null,
        ]);

        $this->selectedCustomerId = $customer->id;
        $this->showCustomerForm = false;
        $this->customerName = '';
    }

    public function clearCustomer(): void
    {
        $this->reset(['selectedCustomerId', 'customerPhone', 'customerName', 'showCustomerForm']);
    }

    public function applyCoupon(): void
    {
        Gate::authorize('apply pos discounts');

        $coupon = Coupon::where('code', strtoupper($this->couponCode))->first();

        if (! $coupon || ! $coupon->isValid() || $coupon->calculateDiscount($this->subtotal()) <= 0) {
            session()->flash('error', __('Invalid or inapplicable coupon code.'));

            return;
        }

        $this->appliedCouponId = $coupon->id;
    }

    public function removeCoupon(): void
    {
        $this->appliedCouponId = null;
        $this->couponCode = '';
    }

    public function holdSale(): void
    {
        Gate::authorize('hold pos sales');

        if (empty($this->cart)) {
            session()->flash('error', __('There is nothing in the cart to hold.'));

            return;
        }

        PosHeldSale::create([
            'register_id' => $this->registerId,
            'held_by' => Auth::id(),
            'customer_id' => $this->selectedCustomerId,
            'cart_snapshot' => [
                'cart' => $this->cart,
                'discountAmount' => $this->discountAmount,
                'couponCode' => $this->couponCode,
                'appliedCouponId' => $this->appliedCouponId,
                'notes' => $this->notes,
            ],
            'note' => $this->holdNote ?: null,
            'held_at' => now(),
        ]);

        $this->reset(['cart', 'selectedCustomerId', 'customerPhone', 'customerName', 'discountAmount', 'couponCode', 'appliedCouponId', 'holdNote', 'notes']);
        unset($this->heldSales);
        session()->flash('message', __('Sale held.'));
    }

    public function resumeHeldSale(int $id): void
    {
        $held = PosHeldSale::findOrFail($id);
        $snapshot = $held->cart_snapshot;

        $this->cart = $snapshot['cart'] ?? [];
        $this->discountAmount = (float) ($snapshot['discountAmount'] ?? 0);
        $this->couponCode = $snapshot['couponCode'] ?? '';
        $this->appliedCouponId = $snapshot['appliedCouponId'] ?? null;
        $this->notes = $snapshot['notes'] ?? '';
        $this->selectedCustomerId = $held->customer_id;

        $held->delete();
        unset($this->heldSales);
    }

    public function discardHeldSale(int $id): void
    {
        PosHeldSale::where('id', $id)->where('register_id', $this->registerId)->delete();
        unset($this->heldSales);
    }

    public function addPayment(): void
    {
        $this->recordPayment($this->paymentMethod, $this->paymentTendered, $this->paymentReference ?: null);
        $this->reset(['paymentTendered', 'paymentReference']);
    }

    /**
     * One-tap cash tender for a common denomination or the exact remaining
     * balance — the fast path a real till uses instead of typing an amount.
     */
    public function quickCash(float $amount): void
    {
        $this->recordPayment('cash', $amount);
    }

    private function recordPayment(string $method, float $tendered, ?string $reference = null): void
    {
        Gate::authorize('process pos sales');

        if ($tendered <= 0) {
            session()->flash('error', __('Enter a tendered amount greater than zero.'));

            return;
        }

        $remaining = $this->remainingDue();

        if ($method === 'store_credit') {
            $customer = $this->selectedCustomer();

            if (! $customer) {
                session()->flash('error', __('Select a customer to pay with store credit.'));

                return;
            }

            if ($tendered > $customer->store_credit_balance) {
                session()->flash('error', __('That customer only has :balance in store credit.', ['balance' => number_format($customer->store_credit_balance, 2)]));

                return;
            }
        }

        if ($method !== 'cash' && $tendered > $remaining) {
            session()->flash('error', __('That amount exceeds the remaining balance due.'));

            return;
        }

        $amount = min($tendered, $remaining);
        $change = $method === 'cash' ? round($tendered - $amount, 2) : 0;

        $this->payments[] = [
            'method' => $method,
            'amount' => $amount,
            'reference' => $reference,
            'change_given' => $change,
        ];
    }

    public function removePayment(int $index): void
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function checkout(): void
    {
        Gate::authorize('process pos sales');

        if (empty($this->cart)) {
            session()->flash('error', __('The cart is empty.'));

            return;
        }

        if ($this->remainingDue() > 0) {
            session()->flash('error', __('Payments do not cover the total yet.'));

            return;
        }

        $shift = $this->shift();

        if (! $shift) {
            session()->flash('error', __('Open a shift before processing a sale.'));

            return;
        }

        $customer = $this->selectedCustomer();

        $lines = collect($this->cart)->map(fn ($line) => [
            'product_id' => $line['product_id'],
            'product_attribute_id' => $line['product_attribute_id'],
            'product_name' => $line['product_name'],
            'attribute_data' => $line['attribute_data'],
            'quantity' => $line['quantity'],
            'unit_price' => $line['unit_price'],
        ])->values()->all();

        try {
            $order = DB::transaction(function () use ($lines, $shift, $customer) {
                $order = app(PosSaleService::class)->checkout([
                    'order' => [
                        'channel' => 'pos',
                        'customer_id' => $customer?->id,
                        'register_id' => $this->registerId,
                        'shift_id' => $shift->id,
                        'customer_name' => $customer?->name ?? 'Walk-in Customer',
                        'customer_phone' => $customer?->phone ?? '',
                        'status' => 'delivered',
                        'subtotal' => $this->subtotal(),
                        'discount' => $this->totalDiscount(),
                        'total' => $this->total(),
                        'notes' => $this->notes ?: null,
                    ],
                    'lines' => $lines,
                    'payments' => collect($this->payments)->map(fn ($p) => [
                        'method' => $p['method'],
                        'amount' => $p['amount'],
                        'reference' => $p['reference'],
                        'change_given' => $p['change_given'],
                    ])->all(),
                    'warehouse' => $this->register()->warehouse,
                ]);

                foreach ($this->payments as $payment) {
                    if ($payment['method'] === 'cash') {
                        PosCashMovement::create([
                            'shift_id' => $shift->id,
                            'type' => 'sale_cash',
                            'amount' => $payment['amount'],
                            'reason' => "Order #{$order->order_number}",
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                        ]);
                    }

                    if ($payment['method'] === 'store_credit' && $customer) {
                        $customer->decrement('store_credit_balance', $payment['amount']);
                    }
                }

                if ($this->appliedCouponId) {
                    Coupon::find($this->appliedCouponId)?->incrementUsage();
                }

                return $order;
            });
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->completedOrderId = $order->id;
        $this->lastCompletedOrderId = $order->id;
        $this->reset(['cart', 'selectedCustomerId', 'customerPhone', 'customerName', 'discountAmount', 'couponCode', 'appliedCouponId', 'payments', 'paymentTendered', 'paymentReference', 'notes']);
        unset($this->shift);
    }

    public function startNewSale(): void
    {
        $this->completedOrderId = null;
    }

    public function reprintLastReceipt(): void
    {
        if ($this->lastCompletedOrderId) {
            $this->reprintOrderId = $this->lastCompletedOrderId;
        }
    }

    public function closeReprint(): void
    {
        $this->reprintOrderId = null;
    }

    public function render()
    {
        return view('livewire.pos.terminal')
            ->layout('components.layouts.pos', ['title' => __('POS Terminal')]);
    }
}
