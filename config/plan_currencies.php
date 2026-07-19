<?php

// Registry of selectable currencies for platform Plan pricing (see
// PlatformSetting keys plan_currency_code / plan_currency_symbol and
// App\Models\Plan::priceLabel()). Unrelated to the per-tenant storefront
// currency_code/currency_symbol Settings, which price products for shoppers.
return [
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'BDT' => '৳',
    'INR' => '₹',
    'JPY' => '¥',
    'AUD' => 'A$',
    'CAD' => 'C$',
];
