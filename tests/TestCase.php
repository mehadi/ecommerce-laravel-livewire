<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // Feature tests hit tenant-scoped routes (storefront/dashboard/admin), which are
    // reached in real usage via a "{slug}.{central domain}" subdomain — bare central
    // domains are reserved for the platform frontend (see App\Http\Middleware\ResolveTenant).
    // "default" matches the tenant slug tests/Pest.php's beforeEach seeds, so requests
    // resolve a tenant the same way production subdomain routing does.
    protected $baseUrl = 'http://default.localhost';
}
