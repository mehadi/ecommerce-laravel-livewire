<?php

namespace App\Livewire\Admin\Attributes;

use App\Models\Attribute;
use App\Models\AttributeValue;
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
            $this->slug = \Illuminate\Support\Str::slug($this->name);
        }
    }

    public function saveAttribute(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:attributes,slug,'.($this->editingAttribute?->id ?? 'NULL'),
            'type' => 'required|in:text,number,decimal',
            'unit' => 'nullable|string|max:50',
            'is_required' => 'boolean',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

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
        $this->validate([
            'valueName' => 'required|string|max:255',
            'valueDisplay' => 'nullable|string|max:255',
            'valueOrder' => 'integer|min:0',
            'valueIsActive' => 'boolean',
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
        $value->delete();
        $this->selectedAttribute->refresh();
        $this->attributeValues = $this->selectedAttribute->values()->orderBy('order')->get()->toArray();
        session()->flash('message', __('Attribute value deleted successfully.'));
    }

    public function render()
    {
        $allAttributes = Attribute::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
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
