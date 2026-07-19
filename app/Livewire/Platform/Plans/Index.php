<?php

namespace App\Livewire\Platform\Plans;

use App\Models\Plan;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $plan_currency_code = 'USD';

    public string $name = '';

    public string $slug = '';

    public $price = 0;

    public string $billing_period = 'monthly';

    public $max_products = null;

    public $max_admin_users = null;

    public $max_custom_domains = null;

    public array $features = [];

    public bool $is_default = false;

    public function mount(): void
    {
        Gate::authorize('access platform');

        $this->plan_currency_code = PlatformSetting::get('plan_currency_code', 'USD');
    }

    public function updateCurrency(): void
    {
        $currencies = config('plan_currencies', []);

        $this->validate([
            'plan_currency_code' => ['required', 'string', 'in:'.implode(',', array_keys($currencies))],
        ]);

        PlatformSetting::setMany([
            'plan_currency_code' => $this->plan_currency_code,
            'plan_currency_symbol' => $currencies[$this->plan_currency_code],
        ]);

        session()->flash('message', __('Currency updated successfully.'));
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug,'.$this->editingId,
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:'.implode(',', Plan::BILLING_PERIODS),
            'max_products' => 'nullable|integer|min:0',
            'max_admin_users' => 'nullable|integer|min:0',
            'max_custom_domains' => 'nullable|integer|min:0',
            'is_default' => 'boolean',
        ];
    }

    public function createPlan(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'price', 'billing_period', 'max_products', 'max_admin_users', 'max_custom_domains', 'is_default']);
        $this->features = array_fill_keys(array_keys(config('plan_features', [])), false);
        $this->showModal = true;
    }

    public function editPlan(Plan $plan): void
    {
        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->price = $plan->price;
        $this->billing_period = $plan->billing_period;
        $this->max_products = $plan->max_products;
        $this->max_admin_users = $plan->max_admin_users;
        $this->max_custom_domains = $plan->max_custom_domains;
        $this->features = array_fill_keys(array_keys(config('plan_features', [])), false);
        foreach ($plan->features ?? [] as $enabledKey) {
            $this->features[$enabledKey] = true;
        }
        $this->is_default = $plan->is_default;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    protected function payload(): array
    {
        $validated = $this->validate();
        $validated['features'] = array_keys(array_filter($this->features));

        return $validated;
    }

    public function storePlan(): void
    {
        $validated = $this->payload();

        if ($validated['is_default']) {
            Plan::query()->update(['is_default' => false]);
        }

        $validated['sort_order'] = (Plan::max('sort_order') ?? 0) + 1;

        Plan::create($validated);

        session()->flash('message', __('Plan created successfully.'));
        $this->showModal = false;
    }

    public function updatePlan(): void
    {
        $validated = $this->payload();

        if ($validated['is_default']) {
            Plan::where('id', '!=', $this->editingId)->update(['is_default' => false]);
        }

        Plan::findOrFail($this->editingId)->update($validated);

        session()->flash('message', __('Plan updated successfully.'));
        $this->showModal = false;
    }

    public function deletePlan(int $planId): void
    {
        $plan = Plan::withCount('tenants')->findOrFail($planId);

        if ($plan->is_default) {
            session()->flash('error', __('The default plan cannot be deleted. Set another plan as default first.'));

            return;
        }

        if ($plan->tenants_count > 0) {
            session()->flash('error', __('This plan has tenants assigned to it and cannot be deleted.'));

            return;
        }

        $plan->delete();

        session()->flash('message', __('Plan deleted successfully.'));
    }

    public function setDefault(int $planId): void
    {
        Plan::query()->update(['is_default' => false]);
        Plan::where('id', $planId)->update(['is_default' => true]);

        session()->flash('message', __('Default plan updated.'));
    }

    public function moveUp(int $planId): void
    {
        $this->swapSortOrder($planId, 'up');
    }

    public function moveDown(int $planId): void
    {
        $this->swapSortOrder($planId, 'down');
    }

    protected function swapSortOrder(int $planId, string $direction): void
    {
        $plans = Plan::orderBy('sort_order')->orderBy('id')->get();
        $index = $plans->search(fn ($plan) => $plan->id === $planId);

        if ($index === false) {
            return;
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if (! $plans->has($swapIndex)) {
            return;
        }

        $current = $plans[$index];
        $swap = $plans[$swapIndex];

        [$currentOrder, $swapOrder] = [$current->sort_order, $swap->sort_order];

        $current->update(['sort_order' => $swapOrder]);
        $swap->update(['sort_order' => $currentOrder]);
    }

    public function render()
    {
        return view('livewire.platform.plans.index', [
            'plans' => Plan::withCount('tenants')->orderBy('sort_order')->orderBy('id')->get(),
            'featureRegistry' => config('plan_features', []),
            'currencyRegistry' => config('plan_currencies', []),
        ])->layout('components.layouts.app', [
            'title' => __('Plans'),
        ]);
    }
}
