<?php

namespace App\Livewire\Admin\Products;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Support\Tenancy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public $category_id;

    public $name_en = '';

    public $name_bn = '';

    public $description_en = '';

    public $description_bn = '';

    public $ingredients_en = '';

    public $ingredients_bn = '';

    public $benefits_en = '';

    public $benefits_bn = '';

    public $price = 0;

    public $compare_at_price;

    public $buying_price;

    public $sku;

    public $stock = 0;

    public $primary_image;

    public $gallery_images = [];

    public $existing_gallery = [];

    public array $pendingAttachments = [];

    public $is_active = true;

    public $is_featured = false;

    public $order = 0;

    // Attribute system
    public array $selectedAttributes = []; // ['attribute_id' => [value_ids]]

    public array $productAttributes = []; // Generated combinations with pricing

    public function mount(?Product $product = null): void
    {
        if ($product) {
            $this->product = $product;
            $this->category_id = $product->category_id;
            $this->name_en = $product->name_en;
            $this->name_bn = $product->name_bn;
            $this->description_en = $product->description_en;
            $this->description_bn = $product->description_bn;
            $this->ingredients_en = $product->ingredients_en;
            $this->ingredients_bn = $product->ingredients_bn;
            $this->benefits_en = $product->benefits_en;
            $this->benefits_bn = $product->benefits_bn;
            $this->price = $product->price;
            $this->compare_at_price = $product->compare_at_price;
            $this->buying_price = $product->buying_price;
            $this->sku = $product->sku;
            $this->stock = $product->stock;
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
                'stock' => $productAttribute->stock,
                'weight_kg' => $productAttribute->weight_kg,
                'is_active' => $productAttribute->is_active,
            ];
        }

        // Reconstruct selectedAttributes from product attributes
        $this->reconstructSelectedAttributes();
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
                // Find the attribute by name
                $attribute = Attribute::where('name', $attributeName)
                    ->orWhere('slug', strtolower($attributeName))
                    ->first();

                if (! $attribute) {
                    continue;
                }

                // Find the attribute value
                $value = $attribute->values()
                    ->where('value', $attributeValue)
                    ->orWhere('display_value', $attributeValue)
                    ->first();

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
        return [
            'category_id' => 'nullable|exists:categories,id',
            'name_en' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'ingredients_en' => 'nullable|string',
            'ingredients_bn' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'benefits_bn' => 'nullable|string',
            'price' => [function ($attribute, $value, $fail) {
                if (empty($this->productAttributes) && (empty($value) || $value < 0)) {
                    $fail(__('Price is required when no attributes are set.'));
                }
            }, 'nullable', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) {
                if ($value && $this->price && $value <= $this->price) {
                    $fail(__('Compare at price must be greater than the selling price.'));
                }
            }],
            'buying_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,'.($this->product?->id ?? 'NULL'),
            'stock' => [function ($attribute, $value, $fail) {
                if (empty($this->productAttributes) && (empty($value) || $value < 0)) {
                    $fail(__('Stock is required when no attributes are set.'));
                }
            }, 'nullable', 'integer', 'min:0'],
            'primary_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'integer|min:0',
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

    public function save(): void
    {
        $this->validate();

        $isEdit = $this->product !== null;

        if (! $isEdit && ! Tenancy::current()?->canAddProduct()) {
            session()->flash('error', __('Your plan\'s product limit has been reached. Upgrade your plan to add more products.'));

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
            'name_en' => $this->name_en,
            'name_bn' => $this->name_bn ?: null,
            'description_en' => $this->normalizeRichText($this->description_en),
            'description_bn' => $this->normalizeRichText($this->description_bn),
            'ingredients_en' => $this->ingredients_en ?: null,
            'ingredients_bn' => $this->ingredients_bn ?: null,
            'benefits_en' => $this->benefits_en ?: null,
            'benefits_bn' => $this->benefits_bn ?: null,
            'price' => $hasAttributes ? 0 : $this->price,
            'compare_at_price' => $hasAttributes ? null : ($this->compare_at_price ?: null),
            'buying_price' => $this->buying_price ?: null,
            'sku' => $this->sku ?: null,
            'stock' => $hasAttributes ? 0 : $this->stock,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ];

        if ($this->primary_image) {
            if ($isEdit && $this->product->primary_image) {
                Storage::disk('public')->delete($this->product->primary_image);
            }
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

        if ($isEdit) {
            $this->product->update($data);
            $product = $this->product;

            // Delete existing attributes
            $this->product->productAttributes()->delete();
        } else {
            $product = Product::create($data);
        }

        // Save product attributes if any
        if (! empty($this->productAttributes)) {
            $this->saveProductAttributes($product);
        }

        Cache::forget(Tenancy::cacheKey('products.featured'));

        session()->flash('message', $isEdit ? __('Product updated successfully.') : __('Product created successfully.'));

        $this->redirect(route('admin.products.index'));
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
        $this->productAttributes = [];

        if (empty($this->selectedAttributes)) {
            return;
        }

        // Get attribute data with values
        $attributeData = [];
        foreach ($this->selectedAttributes as $attributeId => $valueIds) {
            if (empty($valueIds)) {
                continue;
            }

            $attribute = Attribute::with('values')->find($attributeId);
            if (! $attribute) {
                continue;
            }

            $values = $attribute->values()->whereIn('id', $valueIds)->get();
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

        // Create product attribute entries with default pricing
        foreach ($combinations as $combination) {
            $this->productAttributes[] = [
                'attribute_data' => $combination,
                'price' => $this->price ?: 0,
                'compare_at_price' => $this->compare_at_price,
                'buying_price' => $this->buying_price,
                'sku' => '',
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
            $attribute = Attribute::where('name', $key)
                ->orWhere('slug', strtolower($key))
                ->first();

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

    private function saveProductAttributes(Product $product): void
    {
        foreach ($this->productAttributes as $attributeData) {
            if (empty($attributeData['attribute_data']) || ! isset($attributeData['price'])) {
                continue;
            }

            \App\Models\ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_data' => $attributeData['attribute_data'],
                'price' => $attributeData['price'],
                'compare_at_price' => $attributeData['compare_at_price'] ?? null,
                'buying_price' => $attributeData['buying_price'] ?? null,
                'sku' => $attributeData['sku'] ?? null,
                'stock' => $attributeData['stock'] ?? 0,
                'weight_kg' => $attributeData['weight_kg'] ?? null,
                'is_active' => $attributeData['is_active'] ?? true,
            ]);
        }
    }

    public function removePrimaryImage(): void
    {
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
        if (isset($this->existing_gallery[$index])) {
            Storage::disk('public')->delete($this->existing_gallery[$index]);
            unset($this->existing_gallery[$index]);
            $this->existing_gallery = array_values($this->existing_gallery);
        }
    }

    public function getProfitProperty(): float
    {
        if (! $this->buying_price || $this->buying_price <= 0 || ! $this->price || $this->price <= 0) {
            return 0;
        }

        return round($this->price - $this->buying_price, 2);
    }

    public function getProfitPercentageProperty(): float
    {
        if (! $this->buying_price || $this->buying_price <= 0 || ! $this->price || $this->price <= 0) {
            return 0;
        }

        return round((($this->price - $this->buying_price) / $this->buying_price) * 100, 2);
    }

    public function render()
    {
        $isEdit = $this->product !== null;
        $availableAttributes = Attribute::where('is_active', true)->orderBy('order')->orderBy('name')->get();

        return view('livewire.admin.products.create', [
            'categories' => Category::with('parent')->orderBy('order')->orderBy('name_en')->get(),
            'availableAttributes' => $availableAttributes,
            'isEdit' => $isEdit,
        ])->layout('components.layouts.app', [
            'title' => $isEdit ? __('Edit Product') : __('Create Product'),
        ]);
    }

    public function storePendingAttachment(): array
    {
        if (empty($this->pendingAttachments)) {
            return [];
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
