<?php

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

// Every model backed by a table with a tenant_id column must use BelongsToTenant,
// so a new tenant-owned model/migration can't accidentally ship unscoped and leak
// data across tenants. Exemptions are all deliberate: `City` is shared reference data;
// `Tenant`/`Domain`/`TenantBillingEvent`/`AdminAuditLog` are platform-level records
// *about* a tenant, not tenant-owned data itself; `User` needs nullable tenant_id for
// platform staff and can't be scoped the same way (see App\Models\User doc/Phase 1 notes).
test('every model with a tenant_id column uses BelongsToTenant', function () {
    $exempt = [
        \App\Models\City::class,
        \App\Models\Tenant::class,
        \App\Models\Domain::class,
        \App\Models\User::class,
        \App\Models\TenantBillingEvent::class,
        \App\Models\AdminAuditLog::class,
    ];

    $modelFiles = glob(app_path('Models/*.php'));

    foreach ($modelFiles as $file) {
        $class = 'App\\Models\\'.basename($file, '.php');

        if (! class_exists($class) || in_array($class, $exempt, true)) {
            continue;
        }

        $model = new $class;
        $table = $model->getTable();

        if (! Schema::hasColumn($table, 'tenant_id')) {
            continue;
        }

        $usesTrait = in_array(BelongsToTenant::class, class_uses_recursive($class), true);

        expect($usesTrait)->toBeTrue(
            "{$class} has a tenant_id column on `{$table}` but does not use BelongsToTenant."
        );
    }
});
