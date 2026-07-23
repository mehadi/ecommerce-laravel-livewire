<?php

namespace App\Livewire\Admin\ShopDisplay;

use App\Livewire\ShopPage;
use App\Models\Setting;
use Livewire\Component;

class Index extends Component
{
    public string $columns_options = '';

    public int $columns_default = 3;

    public string $per_page_options = '';

    public int $per_page_default = 6;

    public function mount(): void
    {
        $this->columns_options = implode(', ', ShopPage::columnOptions());
        $this->columns_default = ShopPage::defaultColumns();

        $this->per_page_options = implode(', ', ShopPage::perPageOptions());
        $this->per_page_default = ShopPage::defaultPerPage();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'columns_options' => ['required', 'string'],
            'columns_default' => ['required', 'integer', 'min:1'],
            'per_page_options' => ['required', 'string'],
            'per_page_default' => ['required', 'integer', 'min:1'],
        ]);

        $columnsOptions = $this->parseOptionsList($validated['columns_options'], min: 2, max: 4);
        $perPageOptions = $this->parseOptionsList($validated['per_page_options'], min: 1, max: 200);

        if (empty($columnsOptions)) {
            $this->addError('columns_options', __('Enter at least one whole number between 2 and 4.'));

            return;
        }

        if (empty($perPageOptions)) {
            $this->addError('per_page_options', __('Enter at least one whole number between 1 and 200.'));

            return;
        }

        if (! in_array($validated['columns_default'], $columnsOptions, true)) {
            $this->addError('columns_default', __('Default columns must be one of the listed column options.'));

            return;
        }

        if (! in_array($validated['per_page_default'], $perPageOptions, true)) {
            $this->addError('per_page_default', __('Default per page must be one of the listed per-page options.'));

            return;
        }

        Setting::setMany([
            'shop_columns_options' => json_encode($columnsOptions),
            'shop_columns_default' => (string) $validated['columns_default'],
            'shop_per_page_options' => json_encode($perPageOptions),
            'shop_per_page_default' => (string) $validated['per_page_default'],
        ]);

        // Both settings feed the storefront grid's presentation only, but re-sync
        // form state to the normalized (deduped, sorted) values that were saved.
        $this->columns_options = implode(', ', $columnsOptions);
        $this->per_page_options = implode(', ', $perPageOptions);

        session()->flash('message', __('Shop page display settings saved successfully.'));
    }

    /**
     * Parse a comma-separated list of positive whole numbers, clamped to [min, max],
     * deduped and sorted ascending. Non-numeric entries are silently dropped.
     */
    private function parseOptionsList(string $raw, int $min, int $max): array
    {
        $values = array_map('trim', explode(',', $raw));
        $values = array_filter($values, fn ($v) => $v !== '' && ctype_digit($v));
        $values = array_map('intval', $values);
        $values = array_filter($values, fn ($v) => $v >= $min && $v <= $max);
        $values = array_values(array_unique($values));
        sort($values);

        return $values;
    }

    public function render()
    {
        return view('livewire.admin.shop-display.index')
            ->layout('components.layouts.app', [
                'title' => __('Shop Page Display'),
            ]);
    }
}
