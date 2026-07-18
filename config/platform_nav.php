<?php

// Config-driven Platform (SaaS-operator) admin sidebar. Each new Platform page
// registers itself here instead of requiring a Blade edit to
// resources/views/components/layouts/app/sidebar.blade.php.
return [
    [
        'label' => 'Dashboard',
        'route' => 'platform.dashboard',
        'route_pattern' => 'platform.dashboard',
        'icon' => 'home',
    ],
    [
        'label' => 'Tenants',
        'route' => 'platform.tenants.index',
        'route_pattern' => 'platform.tenants.*',
        'icon' => 'building-office',
    ],
    [
        'label' => 'Upgrade Requests',
        'route' => 'platform.upgrade-requests.index',
        'route_pattern' => 'platform.upgrade-requests.*',
        'icon' => 'arrow-trending-up',
    ],
    [
        'label' => 'Plans',
        'route' => 'platform.plans.index',
        'route_pattern' => 'platform.plans.*',
        'icon' => 'credit-card',
    ],
    [
        'label' => 'Billing',
        'route' => 'platform.billing.index',
        'route_pattern' => 'platform.billing.*',
        'icon' => 'banknotes',
    ],
    [
        'label' => 'Analytics',
        'route' => 'platform.analytics.index',
        'route_pattern' => 'platform.analytics.*',
        'icon' => 'chart-bar',
    ],
    [
        'label' => 'Website Defaults',
        'route' => 'platform.website-defaults.index',
        'route_pattern' => 'platform.website-defaults.*',
        'icon' => 'globe-alt',
    ],
    [
        'label' => 'Settings',
        'route' => 'platform.settings.index',
        'route_pattern' => 'platform.settings.*',
        'icon' => 'cog-6-tooth',
    ],
];
