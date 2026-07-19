<?php

namespace App\Actions\Fortify;

use App\Models\PlatformSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\WelcomeTenant;
use App\Support\Tenancy;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\PermissionRegistrar;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Slugs that would otherwise read as a system/platform host rather than
     * someone's store (e.g. "www.ourplatform.com" or "admin.ourplatform.com").
     */
    protected const RESERVED_SLUGS = ['www', 'api', 'admin', 'platform', 'app', 'static', 'cdn', 'mail', 'ftp'];

    /**
     * Validate and create a newly registered user.
     *
     * /register is only reachable on the central domain — see Gate::define('access
     * platform') in AppServiceProvider, which treats tenant_id === null as platform
     * staff. Registering from an existing tenant's own domain is blocked outright:
     * there's no customer-account concept in this app (checkout is guest-only), so
     * the only legitimate reason to hit /register is to create a brand-new store.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        if (Tenancy::check()) {
            throw ValidationException::withMessages([
                'store_name' => __("Sign-ups aren't available here — visit the main site to create your own store."),
            ]);
        }

        Validator::make($input, [
            'store_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return DB::transaction(fn () => $this->createTenantAndOwner($input));
            } catch (UniqueConstraintViolationException $e) {
                if (! str_contains($e->getMessage(), 'tenants_slug_unique')) {
                    throw $e;
                }
                // Another signup claimed the same slug between our uniqueness check and
                // the insert — regenerate (the loop inside uniqueSlug() will pick the next
                // available suffix) and retry.
            }
        }

        throw ValidationException::withMessages([
            'store_name' => __('Could not create a store with that name right now. Please try again.'),
        ]);
    }

    protected function createTenantAndOwner(array $input): User
    {
        $tenant = Tenant::create([
            'name' => $input['store_name'],
            'slug' => $this->uniqueSlug($input['store_name']),
        ]);

        $owner = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'tenant_id' => $tenant->id,
        ]);

        $registrar = app(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();

        $registrar->setPermissionsTeamId($tenant->id);
        (new RolesPermissionsSeeder)->run();
        $owner->assignRole('admin');

        $registrar->setPermissionsTeamId($previousTeamId);

        $trialDays = (int) PlatformSetting::get('default_trial_days', '14');

        $tenant->update([
            'owner_user_id' => $owner->id,
            'trial_ends_at' => $trialDays > 0 ? now()->addDays($trialDays) : null,
        ]);

        $owner->notify(new WelcomeTenant($tenant));

        return $owner;
    }

    protected function uniqueSlug(string $storeName): string
    {
        $base = Str::slug($storeName);
        $base = $base === '' ? 'store' : rtrim(Str::substr($base, 0, 63), '-');

        $slug = $base;
        $suffix = 2;

        while (in_array($slug, self::RESERVED_SLUGS, true) || Tenant::where('slug', $slug)->exists()) {
            $suffixString = '-'.$suffix++;
            $slug = rtrim(Str::substr($base, 0, 63 - strlen($suffixString)), '-').$suffixString;
        }

        return $slug;
    }
}
