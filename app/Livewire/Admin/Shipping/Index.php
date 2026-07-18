<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\City;
use App\Models\ShippingCityRate;
use App\Models\ShippingSetting;
use Livewire\Component;

class Index extends Component
{
    public $type = 'flat';

    public $flatRate = 0;

    public $baseWeightKg = 1.00;

    public $baseRate = 0;

    public $perKgRate = 0;

    public $isActive = true;

    public $showCityRateModal = false;

    public $editingCityRateId = null;

    public $selectedCityId = null;

    public $isRestOfAllCities = false;

    public $cityBaseRate = 0;

    public $cityPerKgRate = 0;

    public $cityBaseWeightKg = 1.00;

    public $cityIsActive = true;

    public $searchCity = '';

    public function updatedIsRestOfAllCities($value): void
    {
        if ($value) {
            $this->selectedCityId = null;
        }
    }

    protected function rules(): array
    {
        return [
            'type' => 'required|in:flat,weight,city',
            'flatRate' => 'required_if:type,flat|nullable|numeric|min:0',
            'baseWeightKg' => 'required|numeric|min:0.01',
            'baseRate' => 'required_if:type,weight,city|nullable|numeric|min:0',
            'perKgRate' => 'required_if:type,weight,city|nullable|numeric|min:0',
            'isActive' => 'boolean',
        ];
    }

    public function mount(): void
    {
        $setting = ShippingSetting::getActive();

        if ($setting) {
            $this->type = $setting->type;
            $this->flatRate = $setting->flat_rate ?? 0;
            $this->baseWeightKg = $setting->base_weight_kg ?? 1.00;
            $this->baseRate = $setting->base_rate ?? 0;
            $this->perKgRate = $setting->per_kg_rate ?? 0;
            $this->isActive = $setting->is_active;
        }
    }

    public function save(): void
    {
        $this->validate();

        // Deactivate all existing settings
        ShippingSetting::query()->update(['is_active' => false]);

        // Create or update the active setting
        ShippingSetting::updateOrCreate(
            ['is_active' => true],
            [
                'type' => $this->type,
                'flat_rate' => $this->type === 'flat' ? $this->flatRate : null,
                'base_weight_kg' => $this->baseWeightKg,
                'base_rate' => in_array($this->type, ['weight', 'city']) ? $this->baseRate : null,
                'per_kg_rate' => in_array($this->type, ['weight', 'city']) ? $this->perKgRate : null,
                'is_active' => $this->isActive,
            ]
        );

        session()->flash('message', __('Shipping settings saved successfully.'));
        $this->dispatch('saved');
    }

    public function openCityRateModal(?int $cityRateId = null): void
    {
        $this->resetCityRateForm();

        if ($cityRateId) {
            $cityRate = ShippingCityRate::findOrFail($cityRateId);
            $this->editingCityRateId = $cityRate->id;
            $this->isRestOfAllCities = $cityRate->city_id === null;
            $this->selectedCityId = $cityRate->city_id;
            $this->cityBaseRate = $cityRate->base_rate;
            $this->cityPerKgRate = $cityRate->per_kg_rate;
            $this->cityBaseWeightKg = $cityRate->base_weight_kg;
            $this->cityIsActive = $cityRate->is_active;
        }

        $this->showCityRateModal = true;
    }

    public function openRestOfAllCitiesModal(): void
    {
        $this->resetCityRateForm();

        // Check if "Rest of All Cities" rate already exists
        $restOfAllCitiesRate = ShippingCityRate::whereNull('city_id')->first();

        if ($restOfAllCitiesRate) {
            $this->editingCityRateId = $restOfAllCitiesRate->id;
            $this->isRestOfAllCities = true;
            $this->cityBaseRate = $restOfAllCitiesRate->base_rate;
            $this->cityPerKgRate = $restOfAllCitiesRate->per_kg_rate;
            $this->cityBaseWeightKg = $restOfAllCitiesRate->base_weight_kg;
            $this->cityIsActive = $restOfAllCitiesRate->is_active;
        } else {
            $this->isRestOfAllCities = true;
        }

        $this->showCityRateModal = true;
    }

    public function closeCityRateModal(): void
    {
        $this->showCityRateModal = false;
        $this->resetCityRateForm();
    }

    public function saveCityRate(): void
    {
        $rules = [
            'cityBaseRate' => 'required|numeric|min:0',
            'cityPerKgRate' => 'required|numeric|min:0',
            'cityBaseWeightKg' => 'required|numeric|min:0.01',
            'cityIsActive' => 'boolean',
        ];

        if ($this->isRestOfAllCities) {
            // For "Rest of All Cities", city_id should be null
            $this->selectedCityId = null;
        } else {
            $rules['selectedCityId'] = 'required|exists:cities,id';
        }

        $this->validate($rules);

        // Ensure only one "Rest of All Cities" rate exists
        if ($this->isRestOfAllCities) {
            // Delete any existing "Rest of All Cities" rate if we're creating a new one
            // If editing, we'll update the existing one
            if (! $this->editingCityRateId) {
                ShippingCityRate::whereNull('city_id')->delete();
            } elseif ($this->editingCityRateId) {
                // If editing and switching to "Rest of All Cities", delete other "Rest of All Cities" entries
                ShippingCityRate::whereNull('city_id')
                    ->where('id', '!=', $this->editingCityRateId)
                    ->delete();
            }
        }

        ShippingCityRate::updateOrCreate(
            ['city_id' => $this->selectedCityId],
            [
                'base_rate' => $this->cityBaseRate,
                'per_kg_rate' => $this->cityPerKgRate,
                'base_weight_kg' => $this->cityBaseWeightKg,
                'is_active' => $this->cityIsActive,
            ]
        );

        $message = $this->isRestOfAllCities
            ? __('Rest of All Cities rate saved successfully.')
            : __('City rate saved successfully.');

        session()->flash('message', $message);
        $this->closeCityRateModal();
        $this->dispatch('city-rate-saved');
    }

    public function deleteCityRate(int $cityRateId): void
    {
        ShippingCityRate::findOrFail($cityRateId)->delete();
        session()->flash('message', __('City rate deleted successfully.'));
        $this->dispatch('city-rate-deleted');
    }

    public function toggleCityRateStatus(int $cityRateId): void
    {
        $cityRate = ShippingCityRate::findOrFail($cityRateId);
        $cityRate->update(['is_active' => ! $cityRate->is_active]);
        session()->flash('message', __('City rate status updated successfully.'));
        $this->dispatch('city-rate-status-updated');
    }

    protected function resetCityRateForm(): void
    {
        $this->editingCityRateId = null;
        $this->selectedCityId = null;
        $this->isRestOfAllCities = false;
        $this->cityBaseRate = 0;
        $this->cityPerKgRate = 0;
        $this->cityBaseWeightKg = 1.00;
        $this->cityIsActive = true;
    }

    public function render()
    {
        $cityRates = ShippingCityRate::with('city')
            ->when($this->searchCity, function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('city_id')
                        ->orWhereHas('city', function ($cityQuery) {
                            $cityQuery->where('name', 'like', '%'.$this->searchCity.'%')
                                ->orWhere('name_bn', 'like', '%'.$this->searchCity.'%');
                        });
                });
            })
            ->orderByRaw('CASE WHEN city_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->get();

        $excludedCityIds = ShippingCityRate::whereNotNull('city_id')->pluck('city_id');
        if ($this->editingCityRateId && $this->selectedCityId && ! $this->isRestOfAllCities) {
            $excludedCityIds = $excludedCityIds->reject(fn ($id) => $id === $this->selectedCityId);
        }

        $availableCities = City::active()
            ->ordered()
            ->whereNotIn('id', $excludedCityIds)
            ->get();

        $restOfAllCitiesRate = ShippingCityRate::whereNull('city_id')->first();

        $setting = ShippingSetting::getActive();

        return view('livewire.admin.shipping.index', [
            'cityRates' => $cityRates,
            'availableCities' => $availableCities,
            'restOfAllCitiesRate' => $restOfAllCitiesRate,
            'setting' => $setting,
        ])->layout('components.layouts.app', [
            'title' => __('Shipping Management'),
        ]);
    }
}
