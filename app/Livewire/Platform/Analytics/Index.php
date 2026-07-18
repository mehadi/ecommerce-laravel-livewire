<?php

namespace App\Livewire\Platform\Analytics;

use App\Livewire\Platform\Analytics\Concerns\HasPlatformAnalytics;
use App\Models\Tenant;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    use HasPlatformAnalytics;

    public string $startDate;

    public string $endDate;

    public function mount(): void
    {
        Gate::authorize('access platform');

        $this->startDate = now()->subDays(90)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.platform.analytics.index', [
            'activeTenantCount' => Tenant::where('status', 'active')->count(),
        ])->layout('components.layouts.app', [
            'title' => __('Analytics'),
        ]);
    }
}
