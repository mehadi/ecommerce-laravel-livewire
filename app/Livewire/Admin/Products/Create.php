<?php

namespace App\Livewire\Admin\Products;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Supplier;
use App\Support\Tenancy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?Product $product = null;

    public $category_id;

    public $default_supplier_id;

    public $name_en = '';

    public $name_bn = '';

    public $description_en = '';

    public $description_bn = '';

    public $price = 0;

    public $compare_at_price;

    public $buying_price;

    public $sku;

    public $barcode;

    public $stock = 0;

    public $low_stock_threshold;

    public $tracks_batches = false;

    public $primary_image;

    public $gallery_images = [];

    #[Locked]
    public $existing_gallery = [];

    public array $pendingAttachments = [];

    public $is_active = true;

    public $is_featured = false;

    public $order = 0;

    // Attribute system
    public array $selectedAttributes = []; // ['attribute_id' => [value_ids]]

    public array $productAttributes = []; // Generated combinations with pricing

    // Bulk-edit helpers for the variant table
    public $bulkVariantPrice;

    public $bulkVariantStock;

    /**
     * Per-request cache of the tenant's attribute catalog (with values) so the
     * combination helpers below never query inside loops.
     */
    private ?Collection $attributeCatalog = null;

    public function mount(?Product $product = null): void
    {
        $this->product = $product;
        $this->authorizeForm();

        if ($product) {
            $this->category_id = $product->category_id;
            $this->default_supplier_id = $product->default_supplier_id;
            $this->name_en = $product->name_en;
            $this->name_bn = $product->name_bn;
            $this->description_en = $product->description_en;
            $this->description_bn = $product->description_bn;
            $this->price = $product->price;
            $this->compare_at_price = $product->compare_at_price;
            $this->buying_price = $product->buying_price;
            $this->sku = $product->sku;
            $this->barcode = $product->barcode;
            $this->stock = $product->stock;
            $this->low_stock_threshold = $product->low_stock_threshold;
            $this->tracks_batches = (bool) $product->tracks_batches;
            $this->existing_gallery = $product->gallery_images ?? [];
            $this->is_active = $product->is_active;
            $this->is_featured = $product->is_featured;
            $this->order = $product->order;

            // Load existing attributes
            $this->loadProductAttributes();
        }
    }

    private function loadProductAttributes(): void
    {
        if (! $this->product) {
            return;
        }

        $product = $this->product->load('productAttributes');

        // Load existing product attributes
        foreach ($product->productAttributes as $productAttribute) {
            $this->productAttributes[] = [
                'attribute_data' => $productAttribute->attribute_data,
                'price' => $productAttribute->price,
                'compare_at_price' => $productAttribute->compare_at_price,
                'buying_price' => $productAttribute->buying_price,
                'sku' => $productAttribute->sku,
                'barcode' => $productAttribute->barcode,
                'stock' => $productAttribute->stock,
                'weight_kg' => $productAttribute->weight_kg,
                'is_active' => $productAttribute->is_active,
            ];
        }

        // Reconstruct selectedAttributes from product attributes
        $this->reconstructSelectedAttributes();
    }

    /**
     * The tenant's active attribute catalog, memoized per request so the
     * combination helpers can resolve names/values in memory instead of
     * re-querying inside loops.
     */
    private function attributeCatalog(): Collection
    {
        return $this->attributeCatalog ??= Attribute::with(['values', 'activeValues'])
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    private function catalogAttributeByKey(string $key): ?Attribute
    {
        return $this->attributeCatalog()->first(
            fn (Attribute $attribute) => $attribute->name === $key || $attribute->slug === strtolower($key)
        );
    }

    /**
     * Canonical identity of a variant combination, independent of key order,
     * used to match form rows against stored ProductAttribute rows.
     */
    private function combinationKey(array $attributeData): string
    {
        ksort($attributeData);

        return json_encode($attributeData);
    }

    private function reconstructSelectedAttributes(): void
    {
        $this->selectedAttributes = [];

        if (empty($this->productAttributes)) {
            return;
        }

        // Collect all unique attribute-value pairs from product attributes
        $attributeValueMap = [];

        foreach ($this->productAttributes as $productAttribute) {
            $attributeData = $productAttribute['attribute_data'] ?? [];
            if (empty($attributeData)) {
                continue;
            }

            foreach ($attributeData as $attributeName => $attributeValue) {
                $attribute = $this->catalogAttributeByKey($attributeName);

                if (! $attribute) {
                    continue;
                }

                $value = $attribute->values->first(
                    fn ($candidate) => $candidate->value === $attributeValue || $candidate->display_value === $attributeValue
                );

                if ($value) {
                    if (! isset($attributeValueMap[$attribute->id])) {
                        $attributeValueMap[$attribute->id] = [];
                    }
                    if (! in_array($value->id, $attributeValueMap[$attribute->id])) {
                        $attributeValueMap[$attribute->id][] = $value->id;
                    }
                }
            }
        }

        $this->selectedAttributes = $attributeValueMap;
    }

    protected function rules(): array
    {
        $tenantId = Tenancy::id();

        return [
            // Plain 'exists:table,column' runs against the raw DB table and ignores
            // Eloquent's TenantScope global scope, so without scoping this by
            // tenant_id a user could reference another tenant's category/supplier
            // ID here (IDOR) and have it silently attached to their own product.
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'default_supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'name_en' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'price' => [function ($attribute, $value, $fail) {
                // Explicit blank check — empty() would wrongly reject a price of 0.
                if (empty($this->productAttributes) && ($value === null || $value === '')) {
                    $fail(__('Price is required when no attributes are set.'));
                }
            }, 'nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) {
                if ($value && $this->price && $value <= $this->price) {
                    $fail(__('Compare at price must be greater than the selling price.'));
                }
            }],
            'buying_price' => 'nullable|numeric|min:0',
            'sku' => ['nullable', 'string', 'max:255',
                Rule::unique('products', 'sku')
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
                    ->ignore($this->product?->id),
            ],
            'barcode' => ['nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
                    ->ignore($this->product?->id),
            ],
            'stock' => [function ($attribute, $value, $fail) {
                // Batch-tracked stock is derived from batches, so the disabled
                // input legitimately stays blank/0; 0 itself is a valid level.
                if (empty($this->productAttributes) && ! $this->tracks_batches && ($value === null || $value === '')) {
                    $fail(__('Stock is required when no attributes are set.'));
                }
            }, 'nullable', 'integer', 'min:0'],
            'low_stock_threshold' => 'nullable|integer|min:1',
            'tracks_batches' => 'boolean',
            'primary_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'integer|min:0',
            'productAttributes.*.price' => 'required|numeric|min:0',
            'productAttributes.*.compare_at_price' => 'nullable|numeric|min:0',
            'productAttributes.*.buying_price' => 'nullable|numeric|min:0',
            'productAttributes.*.stock' => 'required|integer|min:0',
            'productAttributes.*.sku' => 'nullable|string|max:255|distinct:ignore_case',
            'productAttributes.*.barcode' => 'nullable|string|max:255|distinct:ignore_case',
            'productAttributes.*.weight_kg' => 'nullable|numeric|min:0',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'productAttributes.*.price' => __('variant price'),
            'productAttributes.*.compare_at_price' => __('variant compare at price'),
            'productAttributes.*.buying_price' => __('variant buying price'),
            'productAttributes.*.stock' => __('variant stock'),
            'productAttributes.*.sku' => __('variant SKU'),
            'productAttributes.*.barcode' => __('variant barcode'),
            'productAttributes.*.weight_kg' => __('variant weight'),
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function generateSku(): void
    {
        if (empty($this->name_en)) {
            session()->flash('error', __('Please enter a product name first to generate SKU.'));

            return;
        }

        $baseSku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name_en), 0, 6));

        // Ensure base SKU has at least 2 characters
        if (strlen($baseSku) < 2) {
            $baseSku = 'PROD';
        }

        // Find existing SKUs with similar pattern (excluding current product if editing)
        $query = Product::where('sku', 'like', $baseSku.'%');
        if ($this->product) {
            $query->where('id', '!=', $this->product->id);
        }
        $existingCount = $query->count();
        $number = $existingCount + 1;

        // Generate unique SKU
        $sku = $baseSku.'-'.str_pad($number, 5, '0', STR_PAD_LEFT);

        // Ensure uniqueness (excluding current product if editing)
        $checkQuery = Product::where('sku', $sku);
        if ($this->product) {
            $checkQuery->where('id', '!=', $this->product->id);
        }
        while ($checkQuery->exists()) {
            $number++;
            $sku = $baseSku.'-'.str_pad($number, 5, '0', STR_PAD_LEFT);
            $checkQuery = Product::where('sku', $sku);
            if ($this->product) {
                $checkQuery->where('id', '!=', $this->product->id);
            }
        }

        $this->sku = $sku;

        $this->validateOnly('sku');
    }

    /**
     * Fill empty variant SKUs from the base SKU plus a slug of the
     * combination (e.g. TSHIRT-00001-RED-XL). Never overwrites a SKU the
     * user already typed.
     */
    public function generateVariantSkus(): void
    {
        if (! $this->sku) {
            $this->generateSku();
        }

        if (! $this->sku) {
            return;
        }

        foreach ($this->productAttributes as $index => $row) {
            if (! empty($row['sku'])) {
                continue;
            }

            $suffix = collect($row['attribute_data'] ?? [])
                ->map(fn ($value) => strtoupper(Str::slug((string) $value, '')))
                ->filter()
                ->join('-');

            $this->productAttributes[$index]['sku'] = $suffix ? $this->sku.'-'.$suffix : null;
        }
    }

    public function applyBulkVariantPrice(): void
    {
        if ($this->bulkVariantPrice === null || $this->bulkVariantPrice === '' || $this->bulkVariantPrice < 0) {
            return;
        }

        foreach ($this->productAttributes as $index => $row) {
            $this->productAttributes[$index]['price'] = $this->bulkVariantPrice;
        }
    }

    public function applyBulkVariantStock(): void
    {
        if ($this->bulkVariantStock === null || $this->bulkVariantStock === '' || $this->bulkVariantStock < 0) {
            return;
        }

        foreach ($this->productAttributes as $index => $row) {
            $this->productAttributes[$index]['stock'] = (int) $this->bulkVariantStock;
        }
    }

    /**
     * mount() only runs on the initial load in Livewire 3, so every mutating
     * action re-checks the gate itself (covers mid-session permission revocation).
     */
    private function authorizeForm(): void
    {
        Gate::authorize($this->product ? 'edit products' : 'create products');
    }

    public function save(): void
    {
        $this->authorizeForm();

        try {
            $this->validate();
        } catch (ValidationException $exception) {
            // Lets the view reset its "saving" guard and scroll to the error summary.
            $this->dispatch('product-form-invalid');

            throw $exception;
        }

        $isEdit = $this->product !== null;

        if (! $isEdit && ! Tenancy::current()?->canAddProduct()) {
            session()->flash('error', __('Your plan\'s product limit has been reached. Upgrade your plan to add more products.'));
            $this->dispatch('product-form-invalid');

            return;
        }

        // Auto-set order if not provided (only for create)
        if (! $isEdit && ($this->order === null || $this->order === 0)) {
            $maxOrder = Product::max('order');
            $this->order = $maxOrder ? $maxOrder + 1 : 0;
        }

        // If product attributes exist, use 0 for base product price/stock
        $hasAttributes = ! empty($this->productAttributes);

        $data = [
            'category_id' => $this->category_id ?: null,
            'default_supplier_id' => $this->default_supplier_id ?: null,
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn ?: null,
            'description_en' => $this->normalizeRichText($this->description_en),
            'description_bn' => $this->normalizeRichText($this->description_bn),
            'price' => $hasAttributes ? 0 : $this->price,
            'compare_at_price' => $hasAttributes ? null : ($this->compare_at_price ?: null),
            'buying_price' => $this->buying_price ?: null,
            'sku' => $this->sku ?: null,
            'barcode' => $this->barcode ?: null,
            // A tracks_batches product's stock is derived entirely from its
            // batches (see ProductBatchObserver) — the manual Stock field is
            // ignored for it, matching the "add a batch to add stock" flow.
            'stock' => ($hasAttributes || $this->tracks_batches) ? 0 : $this->stock,
            'low_stock_threshold' => $this->low_stock_threshold ?: null,
            'tracks_batches' => ! $hasAttributes && $this->tracks_batches,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ];

        // File storage isn't transactional, so store uploads up front and only
        // delete the replaced primary image after the DB work commits.
        $replacedPrimaryImage = null;
        if ($this->primary_image) {
            $replacedPrimaryImage = $isEdit ? $this->product->primary_image : null;
            $data['primary_image'] = $this->primary_image->store(Tenancy::storagePath('products'), 'public');
        }

        $galleryPaths = $isEdit ? $this->existing_gallery : [];
        if (! empty($this->gallery_images)) {
            foreach ($this->gallery_images as $image) {
                if ($image) {
                    $galleryPaths[] = $image->store(Tenancy::storagePath('products/gallery'), 'public');
                }
            }
        }
        $data['gallery_images'] = $galleryPaths;

        $product = DB::transaction(function () use ($data, $isEdit) {
            if ($isEdit) {
                $this->product->update($data);
                $product = $this->product;
            } else {
                $product = Product::create($data);
            }

            $this->syncProductAttributes($product);

            if (! empty($this->productAttributes)) {
                // The base product was saved with price/stock forced to 0 above
                // (they're tracked per-variant instead) — resync the denormalized
                // cache now that the variants exist, so Products/Inventory show
                // the real totals immediately.
                $product->syncPriceAndStock();
            }

            return $product;
        });

        if ($replacedPrimaryImage) {
            Storage::disk('public')->delete($replacedPrimaryImage);
        }

        Cache::forget(Tenancy::cacheKey('products.featured'));

        session()->flash('message', $isEdit ? __('Product updated successfully.') : __('Product created successfully.'));

        $this->redirect(route('admin.products.index'));
    }

    /**
     * Diff-and-upsert the variant rows instead of delete-all/recreate.
     * Matching by combination keeps ProductAttribute IDs stable, which is
     * what preserves warehouse stock allocations (cascadeOnDelete), the
     * variant-level StockMovement audit trail (cascadeOnDelete), and
     * order-item variant links (nullOnDelete) across edits.
     */
    private function syncProductAttributes(Product $product): void
    {
        $existing = $product->productAttributes()->get()
            ->keyBy(fn (ProductAttribute $row) => $this->combinationKey($row->attribute_data ?? []));

        $keptIds = [];

        foreach ($this->productAttributes as $row) {
            if (empty($row['attribute_data']) || ! isset($row['price'])) {
                continue;
            }

            $payload = [
                'attribute_data' => $row['attribute_data'],
                'price' => $row['price'],
                'compare_at_price' => $this->nullableNumber($row['compare_at_price'] ?? null),
                'buying_price' => $this->nullableNumber($row['buying_price'] ?? null),
                'sku' => ($row['sku'] ?? '') !== '' ? $row['sku'] : null,
                'barcode' => ($row['barcode'] ?? '') !== '' ? $row['barcode'] : null,
                'stock' => (int) ($row['stock'] ?? 0),
                'weight_kg' => $this->nullableNumber($row['weight_kg'] ?? null),
                'is_active' => $row['is_active'] ?? true,
            ];

            $current = $existing->get($this->combinationKey($row['attribute_data']));

            if ($current) {
                $current->update($payload);
                $keptIds[] = $current->id;
            } else {
                $keptIds[] = $product->productAttributes()->create($payload)->id;
            }
        }

        // Delete only combinations the user actually removed — one by one so
        // model events still fire (a mass delete would skip observers).
        $product->productAttributes()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each
            ->delete();
    }

    private function nullableNumber($value): mixed
    {
        return ($value === null || $value === '') ? null : $value;
    }

    public function toggleAttribute(int $attributeId): void
    {
        // Check if attribute is currently selected and has values
        $isSelected = isset($this->selectedAttributes[$attributeId]) && ! empty($this->selectedAttributes[$attributeId]);

        if ($isSelected) {
            // Remove attribute and all its values
            unset($this->selectedAttributes[$attributeId]);
        } else {
            // Add attribute with empty values array (user will select values)
            // This allows the values section to be shown
            $this->selectedAttributes[$attributeId] = [];
        }

        // Regenerate product attributes
        $this->generateProductAttributes();
    }

    public function toggleAttributeValue(int $attributeId, int $valueId): void
    {
        // Ensure attribute is in selectedAttributes
        if (! isset($this->selectedAttributes[$attributeId])) {
            $this->selectedAttributes[$attributeId] = [];
        }

        $values = $this->selectedAttributes[$attributeId] ?? [];

        // Toggle value
        if (in_array($valueId, $values)) {
            $values = array_values(array_diff($values, [$valueId]));
        } else {
            $values[] = $valueId;
        }

        // Update or remove attribute
        if (empty($values)) {
            unset($this->selectedAttributes[$attributeId]);
        } else {
            $this->selectedAttributes[$attributeId] = $values;
        }

        // Regenerate product attributes
        $this->generateProductAttributes();
    }

    public function generateProductAttributes(): void
    {
        // Index the rows the user already configured so toggling one value
        // never wipes pricing/stock/SKUs typed into the other combinations.
        $previousRows = collect($this->productAttributes)
            ->keyBy(fn (array $row) => $this->combinationKey($row['attribute_data'] ?? []));

        $this->productAttributes = [];

        if (empty($this->selectedAttributes)) {
            return;
        }

        // Get attribute data with values (from the memoized catalog — no queries)
        $attributeData = [];
        foreach ($this->selectedAttributes as $attributeId => $valueIds) {
            if (empty($valueIds)) {
                continue;
            }

            $attribute = $this->attributeCatalog()->firstWhere('id', $attributeId);
            if (! $attribute) {
                continue;
            }

            $values = $attribute->values->whereIn('id', $valueIds)->values();
            if ($values->isEmpty()) {
                continue;
            }

            $attributeData[] = [
                'attribute' => $attribute,
                'values' => $values,
            ];
        }

        if (empty($attributeData)) {
            return;
        }

        // Generate all combinations
        $combinations = $this->cartesianProductForAttributes($attributeData);

        foreach ($combinations as $combination) {
            $previous = $previousRows->get($this->combinationKey($combination));

            if ($previous) {
                $this->productAttributes[] = $previous;

                continue;
            }

            $this->productAttributes[] = [
                'attribute_data' => $combination,
                'price' => $this->price ?: 0,
                'compare_at_price' => $this->compare_at_price,
                'buying_price' => $this->buying_price,
                'sku' => '',
                'barcode' => '',
                'stock' => 0,
                'weight_kg' => $this->extractWeightFromCombination($combination),
                'is_active' => true,
            ];
        }
    }

    private function cartesianProductForAttributes(array $attributeData): array
    {
        if (empty($attributeData)) {
            return [];
        }

        $result = [[]];

        foreach ($attributeData as $data) {
            $attribute = $data['attribute'];
            $values = $data['values'];
            $newResult = [];

            foreach ($result as $product) {
                foreach ($values as $value) {
                    $newResult[] = array_merge($product, [
                        $attribute->name => $value->display_value ?: $value->value,
                    ]);
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    private function extractWeightFromCombination(array $combination): ?float
    {
        // Try to find weight attribute in the combination
        foreach ($combination as $key => $value) {
            $attribute = $this->catalogAttributeByKey($key);

            if ($attribute && $attribute->isWeight()) {
                // Try to extract numeric value
                $numericValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if ($numericValue !== false && $numericValue !== '') {
                    return (float) $numericValue;
                }
            }
        }

        return null;
    }

    /**
     * Clone the product being edited as an inactive draft copy and jump to it.
     */
    public function duplicate(): void
    {
        Gate::authorize('create products');

        if (! $this->product) {
            return;
        }

        if (! Tenancy::current()?->canAddProduct()) {
            session()->flash('error', __('Your plan\'s product limit has been reached. Upgrade your plan to add more products.'));

            return;
        }

        $copy = $this->product->duplicate();

        Cache::forget(Tenancy::cacheKey('products.featured'));

        session()->flash('message', __('Product duplicated as an inactive draft. You are now editing the copy.'));

        $this->redirect(route('admin.products.edit', $copy));
    }

    public function removePrimaryImage(): void
    {
        $this->authorizeForm();

        if ($this->product && $this->product->primary_image) {
            Storage::disk('public')->delete($this->product->primary_image);
            $this->product->update(['primary_image' => null]);
        }
        $this->reset('primary_image');
    }

    public function removeGalleryImage(int $index): void
    {
        if (! isset($this->gallery_images[$index])) {
            return;
        }

        unset($this->gallery_images[$index]);

        $this->gallery_images = array_values($this->gallery_images);
    }

    public function removeExistingGalleryImage(int $index): void
    {
        $this->authorizeForm();

        if (! isset($this->existing_gallery[$index])) {
            return;
        }

        Storage::disk('public')->delete($this->existing_gallery[$index]);
        unset($this->existing_gallery[$index]);
        $this->existing_gallery = array_values($this->existing_gallery);

        // The file is gone from disk immediately, so persist the reference
        // removal now too — matching makeGalleryImagePrimary/moveExistingGalleryImage.
        // Otherwise leaving without saving would orphan a dead path in the DB.
        $this->product?->update(['gallery_images' => $this->existing_gallery]);
    }

    /**
     * Promote an existing gallery image to primary, swapping the current
     * primary (if any) back into the gallery. Persists immediately, matching
     * removeExistingGalleryImage's storage semantics.
     */
    public function makeGalleryImagePrimary(int $index): void
    {
        $this->authorizeForm();

        if (! $this->product || ! isset($this->existing_gallery[$index])) {
            return;
        }

        $gallery = $this->existing_gallery;
        $newPrimary = $gallery[$index];

        if ($this->product->primary_image) {
            $gallery[$index] = $this->product->primary_image;
        } else {
            unset($gallery[$index]);
        }

        $this->existing_gallery = array_values($gallery);

        $this->product->update([
            'primary_image' => $newPrimary,
            'gallery_images' => $this->existing_gallery,
        ]);

        $this->reset('primary_image');

        Cache::forget(Tenancy::cacheKey('products.featured'));
    }

    public function moveExistingGalleryImage(int $index, int $direction): void
    {
        $this->authorizeForm();

        $target = $index + ($direction < 0 ? -1 : 1);

        if (! isset($this->existing_gallery[$index], $this->existing_gallery[$target])) {
            return;
        }

        $gallery = $this->existing_gallery;
        [$gallery[$index], $gallery[$target]] = [$gallery[$target], $gallery[$index]];
        $this->existing_gallery = $gallery;

        $this->product?->update(['gallery_images' => $this->existing_gallery]);
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::with('parent')->orderBy('order')->orderBy('name_en')->get();
    }

    #[Computed]
    public function suppliers(): Collection
    {
        return Supplier::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function availableAttributes(): Collection
    {
        return $this->attributeCatalog()->where('is_active', true)->values();
    }

    public function render()
    {
        $isEdit = $this->product !== null;

        return view('livewire.admin.products.create', [
            'isEdit' => $isEdit,
        ])->layout('components.layouts.app', [
            'title' => $isEdit ? __('Edit Product') : __('Create Product'),
        ]);
    }

    public function storePendingAttachment(): array
    {
        $this->authorizeForm();

        if (empty($this->pendingAttachments)) {
            return [];
        }

        try {
            // Same constraints as primary/gallery uploads — without this,
            // arbitrary files (SVG/HTML payloads) could land on the public disk.
            $this->validate(['pendingAttachments.*' => 'image|max:2048']);
        } catch (ValidationException $exception) {
            $this->pendingAttachments = [];

            throw $exception;
        }

        $file = array_shift($this->pendingAttachments);

        if (! $file) {
            return [];
        }

        $path = $file->store(Tenancy::storagePath('products/rich-text'), 'public');

        $this->pendingAttachments = array_values($this->pendingAttachments);

        return [
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
        ];
    }

    private function normalizeRichText(?string $value): ?string
    {
        $normalized = trim($value ?? '');

        return $normalized === '' ? null : $normalized;
    }
}
