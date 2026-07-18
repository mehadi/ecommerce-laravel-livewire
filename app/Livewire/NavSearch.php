<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NavSearch extends Component
{
    public string $query = '';

    public string $variant = 'desktop';

    #[Computed]
    public function results()
    {
        $term = trim($this->query);

        $products = Product::query()
            ->where('is_active', true)
            ->with('category');

        if ($term !== '') {
            $products->where(function ($q) use ($term) {
                $q->where('name_en', 'ilike', "%{$term}%")
                    ->orWhere('name_bn', 'ilike', "%{$term}%");
            })->orderByDesc('is_featured')->orderBy('order');
        } else {
            $products->where('is_featured', true)->orderBy('order');
        }

        return $products->limit(6)->get();
    }

    public function render()
    {
        return view('livewire.nav-search');
    }
}
