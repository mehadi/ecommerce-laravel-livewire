# POS Module — Product & Technical Specification (v1)

Status: **Implemented (Milestones 1–7 complete).** See §12 for build notes and known follow-ups.
Scope decisions confirmed with the user: tenant-side feature, record-only payments (no gateway integration), offline sync deferred, camera barcode scanning deferred (USB/keyboard-wedge scanners supported).

---

## 1. Current System Analysis (summary)

- **Tenancy**: custom, single-database, `tenant_id`-scoped via `BelongsToTenant` trait + `TenantScope` global scope (`app/Models/Concerns/BelongsToTenant.php`). No package like stancl/tenancy. Current tenant resolved by `ResolveTenant` middleware, exposed via `App\Support\Tenancy`.
- **Admin UI**: Livewire full-page components under `app/Livewire/Admin/{Module}/Index.php` (+ `Create.php`/`Edit.php` for line-item-heavy resources), views at `resources/views/livewire/admin/{module}/*.blade.php`, layout `components.layouts.app` → sidebar nav registry at `resources/views/components/layouts/app/sidebar.blade.php`.
- **Design system**: Flux UI (`<flux:modal>`, `<flux:button>`, `<flux:field>`, `<flux:badge>`, `<flux:sidebar.*>`, etc.) + a thin `x-admin.*` kit (`page-header`, `stat-card`, `sortable-th`, `table-empty-state`, `bulk-actions-bar`, `confirm-delete-button`, `icon-button`). Tailwind v4, CSS-first config, no `tailwind.config.js`; `npm run build` required (no live Vite dev server in this deployment per [rebuild-tailwind-assets.md]).
- **Permissions**: `spatie/laravel-permission` with **teams mode** keyed on `tenant_id`. Seeded in `database/seeders/RolesPermissionsSeeder.php`. Roles: `super admin`, `admin`, `manager`, `editor`. Gate `access admin` only allows `super admin|admin|manager` (`AppServiceProvider.php`).
- **Inventory (newest module, `8c83ef6`)**: `Warehouse`, `WarehouseStock` (source of truth for stock), `StockMovement` (append-only audit ledger) + `StockMovementType` enum, written automatically by `WarehouseStockObserver`/`ProductObserver` reading a stack-based `App\Support\StockMovementContext`. Canonical deduction pattern (duplicated today in `HasShoppingCart::placeOrder()` and `Admin/Orders/Index.php::createOrder()`):
  ```php
  $stock = WarehouseStock::findOrCreateFor($warehouseId, $productId, $productAttributeId);
  DB::transaction(function () use ($stock, $qty) {
      $locked = WarehouseStock::whereKey($stock->id)->lockForUpdate()->first();
      StockMovementContext::run(['type' => StockMovementType::Sale, 'reason' => '...', 'changed_by' => auth()->id()],
          fn () => $locked->decrement('stock', $qty));
  });
  ```
- **Orders**: `orders` + `order_items`, plain string `status`/`payment_method` columns, no state-machine class. `OrderObserver` restocks items on cancellation (one-way).
- **Reports**: `app/Livewire/Dashboard/DashboardPageComponent` (abstract base) + `Concerns/HasDashboardAnalytics.php` — canonical pattern for adding new dashboard pages/cards.
- **Notifications**: 7 plain (non-queued) `Notification` classes, dispatched via `$model->owner?->notify(...)`, bell UI in `NotificationBell.php`. Tenant-staff-facing links use `$tenant->primaryUrl()`, not `Tenancy::platformUrl()` (that's platform-staff-only).

## 2. Gap Analysis

| Capability the POS needs | Exists today? | Action |
|---|---|---|
| Barcode field for scan-to-add | No (`sku` only, nullable, non-unique on variants) | Add `barcode` column (products + product_attributes) |
| Customer profiles / phone lookup / purchase history | No (`Order` has plain string `customer_name/email/phone`, no `Customer` model) | New `customers` table + `customer_id` FK on `orders` |
| Store credit / loyalty / gift cards / wallet | No, none at all | Store credit: build minimal balance field on `customers` for v1. Loyalty points, gift cards, wallet: **deferred**, out of v1 scope |
| Tax | No tax model/column anywhere in the app | **Deferred for v1** — POS totals mirror the storefront (`subtotal - discount = total`, no tax term), matching existing zero-tax business model. Flagging for your explicit confirmation — see §11 |
| Split/multiple payment tenders per sale | No (`orders` has one `payment_method` string + one `advance_payment` amount) | New `order_payments` line-item table |
| Refunds / returns | No (only cancel-triggers-restock, no money reversal) | New `order_refunds` table + UI action |
| Cash drawer / shift / till | No, greenfield | New `pos_registers`, `pos_shifts`, `pos_cash_movements` tables |
| Cashier-tier permission (POS access without full admin/backoffice access) | No (`access admin` gate = super admin/admin/manager only) | New `access pos` gate + `cashier` role + POS-specific permission set, POS routes **not** nested under `/admin` |
| Held/suspended sales | No | New `pos_held_sales` table (cart snapshot, not a real Order until finalized) |
| Distinguishing POS sales from storefront/admin-manual orders in reports | No `channel`/`source` column on `orders` | Add nullable `channel` column (`storefront`\|`pos`\|`admin_manual`), backfill existing rows to `storefront` |
| Variant-aware stock deduction at point of sale | Admin manual-order path explicitly skips this for variants (documented shortcut) | POS must do it properly — variant picker + `product_attribute_id`-scoped deduction |

## 3. Integration Points (reuse, never duplicate)

- **Stock deduction**: reuse the `WarehouseStock::findOrCreateFor()` + row-lock + `StockMovementContext::run([Sale/Return])` pattern exactly as storefront checkout does it. Extracted into a new thin `App\Services\PosSaleService`, used by POS **and** by a refactored `Admin/Orders/Index.php::createOrder()` (which today has a documented shortcut skipping stock deduction for variant products — the refactor fixes that as a side effect). Storefront checkout (`HasShoppingCart::placeOrder()`) is left untouched — out of scope, different validation shape (shipping-required, session cart).
- **Products/categories/variants**: `Product`, `ProductAttribute`, `Category` — read-only from POS's perspective except for stock, which goes through the service above.
- **Coupons**: reuse `Coupon::isValid()` / `calculateDiscount()` / `incrementUsage()` as-is.
- **Permissions**: extend `RolesPermissionsSeeder.php` with a new `pos` permission group + `cashier` role, following the exact existing naming convention (`verb resource`).
- **Notifications**: reuse the existing `Notification` + bell pattern (not queued, matching current style) for e.g. "shift closed with variance" alerts to managers, addressed via `$tenant->primaryUrl()`.
- **Dashboard/reports**: extend `DashboardPageComponent` with a new `Pos.php` page (or a `Sales.php` "channel" filter) rather than a bespoke reporting stack.
- **Design system**: POS Terminal screen is the **one deliberate visual exception** — it needs a focused, large-touch-target, full-screen layout unlike the standard admin backoffice (see §7). POS **management** screens (registers, shifts admin, settings, reports) use the standard `x-admin.*` + sidebar pattern like every other module.
- **Testing**: Pest, `tests/Feature/Admin/*Test.php` per module, `Livewire::actingAs($admin)->test(...)`, following `WarehousesTest.php`/`OrdersCreateWarehouseTest.php` patterns.

## 4. Product Requirements (PRD)

### 4.1 Vision
Let tenant staff take in-person sales (retail counter / market stall) directly against the same product catalog, inventory, and order history already powering the online storefront — without leaving the platform or duplicating data.

### 4.2 Roles & Permissions (v1)

New role: **`cashier`** — scoped to POS only, cannot reach `/admin/*` backoffice.
Existing roles (`super admin`, `admin`, `manager`) gain POS permissions automatically (manager: all except register/void; admin/super admin: all).

| Permission | cashier | manager | admin / super admin |
|---|:---:|:---:|:---:|
| `access pos` (reach POS terminal) | ✅ | ✅ | ✅ |
| `process pos sales` | ✅ | ✅ | ✅ |
| `apply pos discounts` | ✅ | ✅ | ✅ |
| `hold pos sales` | ✅ | ✅ | ✅ |
| `void pos sale line` (before payment) | ✅ | ✅ | ✅ |
| `void pos sale` (after payment, same shift) | ❌ | ✅ | ✅ |
| `process pos refunds` | ❌ | ✅ | ✅ |
| `open pos shift` / `close pos shift` (own) | ✅ | ✅ | ✅ |
| `close pos shift` (any cashier's, force-close) | ❌ | ✅ | ✅ |
| `manage cash drawer` (cash in/out) | ✅ (own shift) | ✅ | ✅ |
| `view pos reports` | ❌ | ✅ | ✅ |
| `manage pos registers` | ❌ | ✅ | ✅ |
| `manage pos settings` | ❌ | ❌ | ✅ |

### 4.3 Functional scope (v1 — confirmed cuts in bold)

- **POS Terminal** (the till screen): product search (name/SKU/**barcode**/category), cart (add/remove/qty/variant/line note/custom price override with permission gate, per-line discount, coupon), customer (guest, quick-create, phone-number lookup, purchase history), payment (cash/card-record/mobile-banking-record/store-credit, **split across multiple tenders**, change calculation for cash), hold/resume sale, receipt (print via browser + **PDF**; **email/SMS deferred**, no existing mail-template infra for receipts and no SMS gateway in the codebase at all).
- **Shift management**: open (opening float), close (cash count → variance vs expected), cash-in/cash-out during shift, per-shift sales summary.
- **Refunds**: full or partial (per line), cash or store-credit, restocks inventory via the existing `Return` movement type, does **not** call any payment gateway (there isn't one) — reverses money by recording a negative `order_refunds` row only.
- **Reports**: shift report, cashier performance, POS vs storefront channel breakdown, hourly sales, top products at register — added as new Dashboard cards/page reusing `HasDashboardAnalytics` patterns.
- **Deferred (explicitly out of v1, per your scoping answers + gaps found)**: offline mode/local cache/sync, camera barcode scanning, real payment gateway integration, gift cards, loyalty points, tax, SMS/email receipts, multi-currency at the register.

### 4.4 POS Dashboard (widget list, reusing `x-admin.stat-card` + `DashboardPageComponent` pattern)
Today's POS sales, active shifts, current cash-in-drawer (sum of open shifts), today's refunds, top products sold at register, recent POS transactions.

## 5. Database Design

All new tables use `BelongsToTenant` (auto `tenant_id`, global scope) except where noted. Money columns `decimal(10,2)`, matching existing convention.

```
customers
  id, tenant_id, name, phone (indexed), email (nullable),
  store_credit_balance decimal(10,2) default 0,
  notes (text, nullable), timestamps
  unique [tenant_id, phone] where phone is not null

orders  (ALTER — additive, backward compatible)
  + customer_id  FK -> customers, nullable, nullOnDelete
  + channel      string, default 'storefront'  -- 'storefront'|'pos'|'admin_manual'
  + register_id  FK -> pos_registers, nullable, nullOnDelete
  + shift_id     FK -> pos_shifts, nullable, nullOnDelete
  backfill migration: existing rows -> channel = 'storefront'

order_payments
  id, tenant_id, order_id FK cascade,
  method string ('cash'|'card'|'mobile_banking'|'store_credit'),
  amount decimal(10,2), reference string nullable (card/mobile ref no.),
  change_given decimal(10,2) nullable (cash only),
  created_by FK users nullOnDelete, timestamps

order_refunds
  id, tenant_id, order_id FK cascade, order_item_id FK nullable (nullOnDelete; null = whole-sale refund),
  quantity int nullable, amount decimal(10,2),
  method string ('cash'|'store_credit'),
  reason string, refunded_by FK users nullOnDelete, timestamps

products / product_attributes  (ALTER — additive)
  + barcode string nullable, indexed
  unique [tenant_id, barcode] where barcode is not null (partial index, Postgres, mirrors existing warehouse_stocks pattern)

pos_registers
  id, tenant_id, warehouse_id FK, name, code (unique per tenant), is_active, timestamps

pos_shifts
  id, tenant_id, register_id FK, opened_by FK users, closed_by FK users nullable,
  opening_cash decimal(10,2), closing_cash decimal(10,2) nullable,
  expected_cash decimal(10,2) nullable, variance decimal(10,2) nullable,
  status string default 'open' ('open'|'closed'), notes text nullable,
  opened_at datetime, closed_at datetime nullable

pos_cash_movements
  id, tenant_id, shift_id FK cascade,
  type string ('cash_in'|'cash_out'|'sale_cash'|'refund_cash'),
  amount decimal(10,2), reason string nullable,
  created_by FK users nullOnDelete, created_at
  -- append-only audit ledger, same idiom as stock_movements

pos_held_sales
  id, tenant_id, register_id FK, held_by FK users, customer_id FK nullable,
  cart_snapshot json (line items: product_id, product_attribute_id, quantity, unit_price_override, note),
  note text nullable, held_at datetime
```

Indexes: `stock_movements`-style composite indexes on `pos_cash_movements(shift_id, created_at)`, `orders(channel, created_at)`, `customers(tenant_id, phone)`.

## 6. Component / "API" Specification

This app has **no separate JSON/REST API layer** for admin features — everything is server-rendered Livewire full-page components with public methods as the action surface. The spec below lists Livewire components in place of REST endpoints, each gated by the permission from §4.2.

| Component | Route | Purpose | Key public methods |
|---|---|---|---|
| `App\Livewire\Admin\Pos\Terminal` | `pos.terminal` → `GET /pos` (own route group, not under `/admin`) | The till screen | `searchProducts()`, `addToCart()`, `updateQty()`, `removeLine()`, `applyCoupon()`, `setCustomer()`, `quickCreateCustomer()`, `holdSale()`, `resumeSale($id)`, `checkout()` (validates tenders sum = total, runs `PosSaleService`) |
| `App\Livewire\Admin\Pos\Shift` | `pos.shift` → `GET /pos/shift` | Open/close shift, cash in/out | `openShift()`, `closeShift()`, `recordCashMovement()` |
| `App\Livewire\Admin\Pos\Registers\Index` | `admin.pos.registers.index` | Manage registers (admin-side, modal CRUD like Warehouses) | `createRegister()`, `saveRegister()`, `deleteRegister()` |
| `App\Livewire\Admin\Pos\Shifts\Index` | `admin.pos.shifts.index` | Manager view of all shifts, force-close | `forceCloseShift($id)` |
| `App\Livewire\Admin\Pos\Refunds\Create` | `admin.pos.refunds.create` (or a modal off Orders/Show) | Process a refund/return against an existing order | `addRefundLine()`, `submitRefund()` |
| `App\Livewire\Admin\Pos\Settings` | `admin.pos.settings` | Receipt footer text, default register, low-stock-during-sale toggle | `save()` (writes via `Setting::set()`) |
| `App\Livewire\Dashboard\Pos` (or a `channel` filter on `Sales.php`) | `dashboard.pos` | POS-specific reporting | (Computed properties only, read-only) |

New service: `App\Services\PosSaleService::checkout(array $cart, array $payments, ?Customer $customer, Register $register, Shift $shift): Order` — the single place that: re-validates prices/stock server-side (same anti-tamper posture as `HasShoppingCart::placeOrder()`), opens `DB::transaction`, locks + decrements `WarehouseStock` per line via the existing pattern, creates `Order` (`channel='pos'`) + `OrderItem`s + `OrderPayment`s, applies coupon if present, returns the created `Order`.

## 7. UI/UX Specification

- **POS Terminal** (`/pos`): full-screen, no admin sidebar — a dedicated minimal layout (`components.layouts.pos` or similar) with just a top bar (register name, cashier name, shift status, "close shift" / logout). Left: product grid/search with large touch targets (barcode input auto-focused, Enter-to-add). Right: cart panel (line items, qty steppers, remove), totals, customer picker, payment panel. This is the one screen intentionally different from the rest of the admin design system, because a cashier stares at it all day and needs speed over information density — still themed with the same zinc/Flux color tokens and dark-mode support, just a different layout shape.
- **POS management screens** (registers, shifts list, settings, refunds): standard `x-admin.page-header` + `x-admin.stat-card` + sortable table + `<flux:modal>` CRUD, identical shape to `Warehouses/Index`. Added to the sidebar under a new **"POS"** `<flux:sidebar.group>` (parallel to the existing `Inventory` group), gated `@canany(['view pos reports','manage pos registers'])`.
- **Responsive**: POS Terminal targets tablet/desktop (in-store use); mobile phone layout is out of scope for v1 (a cashier terminal is assumed to be a tablet or PC, not a phone). Management screens follow the existing responsive table patterns already used elsewhere (horizontal scroll on small screens).

## 8. Permission Matrix
See §4.2. Full permission strings to add to `RolesPermissionsSeeder.php`: `access pos`, `process pos sales`, `apply pos discounts`, `hold pos sales`, `void pos sale line`, `void pos sale`, `process pos refunds`, `open pos shift`, `close pos shift`, `force close pos shift`, `manage cash drawer`, `view pos reports`, `manage pos registers`, `manage pos settings`.

## 9. Events / Notifications

No new queue infrastructure exists in this app (no `ShouldQueue` usage) — POS notifications follow the same **synchronous** `Notification` pattern:
- `PosShiftClosedWithVariance` — sent to manager/admin users when `|variance| > 0` on shift close, linking to `pos.shift`s report via `$tenant->primaryUrl()`.
- Low-stock-during-sale reuses the existing `Product::isLowStock()` check inline in the terminal (a live badge on the product card), not a new notification — avoids notification spam mid-sale.

## 10. Implementation Plan (milestones, each a checkpoint)

1. **Foundation** — migrations (all tables in §5), models, permission seeder additions, `cashier` role, `PosSaleService`, refactor `Admin/Orders/Index.php::createOrder()` to call it (fixing the existing variant-skip shortcut), factory/seeder for local testing. Tests: model + service unit tests + regression test on the refactored manual-order screen.
2. **POS Terminal core** — product search/cart/checkout (cash + single tender only first), stock deduction wired through `PosSaleService`, receipt print view. Tests: Feature test mirroring `OrdersCreateWarehouseTest.php`.
3. **Customers + split payments + hold/resume** — customer quick-create/lookup, `order_payments` multi-tender UI, `pos_held_sales`.
4. **Shifts + cash drawer** — open/close, cash in/out, variance, manager force-close, POS management screens (registers/shifts admin).
5. **Refunds/returns** — full + partial, store-credit path.
6. **Reporting** — POS dashboard cards, channel breakdown on existing Sales dashboard.
7. **Polish + QA pass** — permission/security review, regression pass on existing checkout/admin-orders (ensure zero behavior change there), full Pest suite.

Each milestone ends with a review checkpoint before moving to the next, per your "phased with checkpoints" preference.

## 11. Decisions (confirmed)

1. **Tax**: out of scope for v1. POS totals = `subtotal - discount`, same as storefront/admin-manual orders. No tax tables/columns.
2. **Store credit**: simple `store_credit_balance` column on `customers`, adjusted directly — no separate ledger table.
3. **Barcode**: new dedicated `barcode` column (products + product_attributes), separate from `sku`, indexed, unique per tenant where not null.
4. **`Admin/Orders` manual-order screen**: refactored in Milestone 1 to call `PosSaleService::checkout()`, removing the duplicated lock/decrement code. Its variant-skip behavior is preserved via an explicit `skip_stock` line flag (see §12) rather than actually fixed — that screen still has no variant picker UI, so it still can't safely resolve which variant to deduct; building that picker was out of scope for this change.

## 12. Implementation Notes (post-build)

- **Register selection**: `PosRegister` has no `is_default` flag (unlike `Warehouse`). `PosRegister::default()` lazily creates/returns the tenant's first active register, mirroring `Warehouse::default()`'s bootstrap pattern. Fine for the common single-register case; a tenant running multiple registers has no cashier-facing register picker yet — everyone lands on the same "first active" register. Worth adding an `is_default` column + picker if multi-register tenants become common.
- **`Admin/Orders` oversell behavior change**: the old manual-order screen silently decremented stock below zero when oversold. Since the refactor routes it through `PosSaleService`, it now locks and checks availability like the storefront does, and rejects the order with a flash error instead. This only affects admin users manually creating orders that exceed available stock.
- **POS Dashboard** (`Admin\Pos\Dashboard`) is a standalone stats page, not a subclass of `DashboardPageComponent` — that base class's `HasDashboardAnalytics` trait is a large, date-range/status-filter-driven engine built around the whole store; retrofitting a `channel` dimension through it was judged riskier than it was worth for a v1 POS reporting page. It queries `Order`/`OrderItem`/`OrderRefund`/`PosShift` directly, filtered to `channel = 'pos'`. The general Sales dashboard was **not** modified to add a channel breakdown — a reasonable follow-up, not done here.
- **Refund cash-drawer attribution**: `Admin\Pos\Refunds\Index` (a backoffice screen, not the till) attributes a cash refund's `PosCashMovement` to whichever shift is currently open anywhere (`PosShift::where('status','open')->first()`), since the manager processing the refund may not have a shift of their own open. If no shift is open, the refund still records correctly in `order_refunds`; only the cash-drawer ledger entry is skipped.
- **Cart/hold-sale persistence**: Terminal cart state lives in Livewire component memory only; a browser refresh mid-sale loses an unheld cart (mirrors the storefront's own session-cart limitations). "Hold Sale" is the durable checkpoint.
- **Deferred, as originally scoped**: gift cards, loyalty points, tax, offline mode, camera barcode scanning, SMS/email receipts, real payment gateway integration.
- **Verification performed**: full Pest suite (181 passing, the same pre-existing 20 unrelated failures as `main`, confirmed via `git stash` diff), Pint clean, `npm run build` clean, and a full manual browser walkthrough (open shift → search/scan → variant picker → split payment → checkout → receipt → dashboard → shifts → refund → close shift with correct variance).
