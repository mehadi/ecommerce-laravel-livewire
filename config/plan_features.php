<?php

// Registry of known Plan feature-flag keys, used only to render labeled
// checkboxes on the Plan CRUD form (App\Livewire\Platform\Plans\Index).
// Runtime enforcement (Plan::hasFeature() / Tenant::hasFeature()) works with
// any string key regardless of whether it's listed here — this registry is
// just a curated, human-readable subset for the admin UI.
return [
    'coupons_enabled' => 'Coupon management',
    'landing_pages_enabled' => 'Custom landing pages',
    'advanced_analytics_enabled' => 'Advanced dashboard analytics',
];
