# Product Create/Edit Module

Modernized 2026-07-21. One Livewire component handles both flows.

## Architecture

| Piece | Path |
|---|---|
| Component (create + edit) | `app/Livewire/Admin/Products/Create.php` |
| View | `resources/views/livewire/admin/products/create.blade.php` |
| Section card | `resources/views/components/products/form-section.blade.php` (accepts a heroicon name via `icon`, legacy SVG d-strings still work) |
| Pricing grid + live profit | `resources/views/components/products/pricing-section.blade.php` (profit computed client-side in Alpine from deferred entangles) |
| Upload zone | `resources/views/components/media/drag-drop-uploader.blade.php` (keyboard-operable, `label` prop for the accessible name) |
| Duplication | `Product::duplicate()` on the model — used by both the edit page and the products list |

Routes `admin.products.create` / `admin.products.edit` both point at `Create`; `mount(?Product $product)` decides the mode.

## Key behaviors (do not regress)

- **Variant sync is diff-and-upsert, never delete-and-recreate.** `syncProductAttributes()` matches stored rows by a canonical key of `attribute_data` (ksort + json_encode) and updates them in place. This keeps `product_attributes.id` stable, which preserves `warehouse_stocks` (cascadeOnDelete), `stock_movements` audit history (cascadeOnDelete), and `order_items.product_attribute_id` (nullOnDelete). Rows are deleted one-by-one (`->each->delete()`) so observers fire.
- **`generateProductAttributes()` preserves user-entered rows** by the same combination key when attribute values are toggled — only genuinely new combinations get defaults.
- **`save()` wraps DB work in a transaction.** Uploads are stored before it; the replaced primary image is deleted only after commit.
- **SKU/barcode uniqueness is tenant-scoped** via `Rule::unique(...)->where('tenant_id', ...)` (the DB constraint is composite `(tenant_id, sku|barcode)`; a bare `unique:` rule would leak/false-reject across tenants).
- **Stock/price of 0 are valid.** Required-ness uses explicit `null`/`''` checks, and `tracks_batches` products are exempt from the stock requirement (their stock derives from batches; the input is disabled).
- **Authorization**: gates `view/create/edit/delete products` (defined in `AppServiceProvider`, enforced in both components) back the seeded Spatie permissions; `admin`/`manager` roles pass without explicit permission grants.
- **Security**: `$product` and `$existing_gallery` are `#[Locked]`; rich-text attachments (`storePendingAttachment`) validate `image|max:2048`; the storefront JSON-LD block json-encodes with `JSON_HEX_TAG` so descriptions can't break out of the `<script>` tag.

## Livewire performance conventions

- No keystroke-frequency roundtrips: names are `wire:model.blur`, Trix editors use deferred `@entangle`, profit margin is Alpine-only.
- Reference data (`categories`, `suppliers`, `availableAttributes`) are `#[Computed]`; the attribute catalog is memoized per request (`attributeCatalog()`) so combination helpers never query in loops.

## UX features

- Sticky action bar (sm+ only), unsaved-changes guard (beforeunload + `livewire:navigate`), error-summary callout the form scrolls to on failed validation (`product-form-invalid` browser event).
- Auto-generate SKU (product level) and "Fill empty SKUs" for variants; bulk price/stock apply across variants; per-variant active toggle and barcode.
- Gallery: reorder (chevrons), promote to primary (star, swaps with current primary immediately), remove with confirm.
- Duplicate (edit page + list) → inactive draft copy, stock 0, SKU/barcode cleared, image files physically copied.
- Sidebar (Status/Organization/Completeness checklist) is ordered first on mobile.

## Extension points

- New product fields: add property + `mount()` hydration + `rules()` + `$data` in `save()` + a field in the relevant `x-products.form-section`.
- New per-variant fields: add to `loadProductAttributes()`, `generateProductAttributes()` defaults, `syncProductAttributes()` payload, and the variant card in the blade.
- The completeness checklist is a plain blade array in the sidebar — extend freely.

## Tests

`tests/Feature/Admin/ProductsTest.php` — 12 tests covering create/edit, variant ID stability, toggle preservation, zero-stock, batch-tracked, barcodes, tenant-scoped SKU, duplication, authorization, and attachment validation. Note: use PNG fixtures (`UploadedFile::fake()->image('x.png')`) — the app container's GD lacks JPEG support.
