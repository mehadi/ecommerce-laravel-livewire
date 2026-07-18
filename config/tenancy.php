<?php

return [

    /*
     * Hosts treated as the platform/marketing context (no tenant resolved) —
     * e.g. the app's own domain, before a tenant's subdomain or custom domain is added.
     * Comma-separated in CENTRAL_DOMAINS.
     */
    'central_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CENTRAL_DOMAINS', 'localhost,127.0.0.1'))
    ))),

    /*
     * The hostname a tenant's custom-domain CNAME (or the IP their A record) must
     * point at for automatic verification + Caddy on-demand TLS issuance to proceed.
     * Typically your platform's own primary domain, e.g. "shops.yourplatform.com".
     */
    'domain_verification_target' => env('TENANT_DOMAIN_TARGET'),

];
