<?php

namespace App\Livewire\Admin\Attributes;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use App\Support\Tenancy;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showValuesModal = false;

    public $editingAttribute = null;

    public $selectedAttribute = null;

    public $name = '';

    public $slug = '';

    public $type = 'text';

    public $unit = '';

    public $is_required = false;

    public $order = 0;

    public $is_active = true;

    public $valueName = '';

    public $valueDisplay = '';

    public $valueOrder = 0;

    public $valueIsActive = true;

    public $attributeValues = [];

    protected $queryString = ['search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'slug', 'type', 'unit', 'is_required', 'order', 'is_active', 'editingAttribute']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'slug', 'type', 'unit', 'is_required', 'order', 'is_active', 'editingAttribute']);
    }

    public function openEditModal(Attribute $attribute): void
    {
        $this->editingAttribute = $attribute;
        $this->name = $attribute->name;
        $this->slug = $attribute->slug;
        $this->type = $attribute->type;
        $this->unit = $attribute->unit ?? '';
        $this->is_required = $attribute->is_required;
        $this->order = $attribute->order;
        $this->is_active = $attribute->is_active;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->reset(['name', 'slug', 'type', 'unit', 'is_required', 'order', 'is_active', 'editingAttribute']);
    }

    public function updatedName(): void
    {
        if (! $this->editingAttribute) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function saveAttribute(): void
    {
        // unique: rules query the raw table and ignore Attribute's TenantScope,
        // so tenant_id has to be constrained explicitly here to avoid cross-tenant
        // false positives (slug taken by another tenant).
        $tenantId = Tenancy::id();

        $slugRule = Rule::unique('attributes', 'slug')->where(fn ($query) => $query->where('tenant_id', $tenantId));

        if ($this->editingAttribute) {
            $slugRule->ignore($this->editingAttribute->id);
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'type' => 'required|in:text,number,decimal',
            'unit' => 'nullable|string|max:50',
            'is_required' => 'boolean',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($this->editingAttribute && $this->editingAttribute->name !== $this->name && $this->attributeInUse($this->editingAttribute)) {
            $this->addError('name', __('Cannot rename ":name" — it is still used by existing product variants. Update those variants first.', ['name' => $this->editingAttribute->name]));

            return;
        }

        if ($this->editingAttribute) {
            $this->editingAttribute->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'type' => $this->type,
                'unit' => $this->unit ?: null,
                'is_required' => $this->is_required,
                'order' => $this->order,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', __('Attribute updated successfully.'));
            $this->closeEditModal();
        } else {
            Attribute::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'type' => $this->type,
                'unit' => $this->unit ?: null,
                'is_required' => $this->is_required,
                'order' => $this->order,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', __('Attribute created successfully.'));
            $this->closeCreateModal();
        }
    }

    public function deleteAttribute(Attribute $attribute): void
    {
        if ($this->attributeInUse($attribute)) {
            session()->flash('error', __('Cannot delete ":name" — it is still used by existing product variants. Remove it from those variants first.', ['name' => $attribute->name]));

            return;
        }

        $attribute->delete();
        session()->flash('message', __('Attribute deleted successfully.'));
    }

    public function openValuesModal(Attribute $attribute): void
    {
        $this->selectedAttribute = $attribute;
        $this->attributeValues = $attribute->values()->orderBy('order')->get()->toArray();
        $this->showValuesModal = true;
    }

    public function closeValuesModal(): void
    {
        $this->showValuesModal = false;
        $this->reset(['selectedAttribute', 'attributeValues', 'valueName', 'valueDisplay', 'valueOrder', 'valueIsActive']);
    }

    public function addValue(): void
    {
        // unique: rules query the raw table and ignore AttributeValue's TenantScope,
        // so tenant_id has to be constrained explicitly here (alongside attribute_id)
        // to turn a duplicate value into a validation error instead of an uncaught
        // QueryException from the attribute_id+value unique index.
        $tenantId = Tenancy::id();
        $attributeId = $this->selectedAttribute->id;

        $this->validate([
            'valueName' => [
                'required', 'string', 'max:255',
                Rule::unique('attribute_values', 'value')->where(
                    fn ($query) => $query->where('attribute_id', $attributeId)->where('tenant_id', $tenantId)
                ),
            ],
            'valueDisplay' => 'nullable|string|max:255',
            'valueOrder' => 'integer|min:0',
            'valueIsActive' => 'boolean',
        ], [
            'valueName.unique' => __('This value already exists for this attribute.'),
        ]);

        AttributeValue::create([
            'attribute_id' => $this->selectedAttribute->id,
            'value' => $this->valueName,
            'display_value' => $this->valueDisplay ?: null,
            'order' => $this->valueOrder,
            'is_active' => $this->valueIsActive,
        ]);

        $this->reset(['valueName', 'valueDisplay', 'valueOrder', 'valueIsActive']);
        $this->selectedAttribute->refresh();
        $this->attributeValues = $this->selectedAttribute->values()->orderBy('order')->get()->toArray();
        session()->flash('message', __('Attribute value added successfully.'));
    }

    public function deleteValue(AttributeValue $value): void
    {
        if ($this->attributeValueInUse($value)) {
            session()->flash('error', __('Cannot delete ":value" — it is still used by existing product variants. Remove it from those variants first.', ['value' => $value->value]));

            return;
        }

        $value->delete();
        $this->selectedAttribute->refresh();
        $this->attributeValues = $this->selectedAttribute->values()->orderBy('order')->get()->toArray();
        session()->flash('message', __('Attribute value deleted successfully.'));
    }

    /**
     * ProductAttribute stores each variant's picks as a flat JSON object keyed
     * by attribute name (e.g. {"Color": "Red"}), not by attribute_id/value_id —
     * see Products/Create::cartesianProductForAttributes(). So checking whether
     * an Attribute is still referenced means looking for its name as a JSON key.
     */
    protected function attributeInUse(Attribute $attribute): bool
    {
        return ProductAttribute::query()
            ->whereRaw('attribute_data->>? is not null', [$attribute->name])
            ->exists();
    }

    /**
     * Same JSON-object storage as attributeInUse(), but matched on the value
     * stored under the owning attribute's name key. Checked against both the
     * resolved display value and the raw value, matching how
     * cartesianProductForAttributes() picks display_value with a value fallback.
     */
    protected function attributeValueInUse(AttributeValue $value): bool
    {
        $attributeName = $value->attribute->name;

        return ProductAttribute::query()
            ->where(function ($query) use ($attributeName, $value) {
                $query->whereRaw('attribute_data->>? = ?', [$attributeName, $value->display_value])
                    ->orWhereRaw('attribute_data->>? = ?', [$attributeName, $value->value]);
            })
            ->exists();
    }

    public function render()
    {
        $allAttributes = Attribute::query()
            ->when($this->search, function ($query) {
                // Nested so the tenant scope's leading `where tenant_id = ?` stays
                // ANDed with the whole group instead of only the first `where`,
                // which would otherwise let the `orWhere` leak other tenants' rows.
                $query->where(function ($inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('order')->orderBy('name')->paginate(15);

        $stats = [
            'total' => Attribute::count(),
            'active' => Attribute::where('is_active', true)->count(),
            'inactive' => Attribute::where('is_active', false)->count(),
        ];

        return view('livewire.admin.attributes.index', [
            'allAttributes' => $allAttributes,
            'stats' => $stats,
        ])->layout('components.layouts.app', [
            'title' => __('Manage Attributes'),
        ]);
    }
}
