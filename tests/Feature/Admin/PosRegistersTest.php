<?php

declare(strict_types=1);

use App\Livewire\Admin\Pos\Registers\Index;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('a non-manager cannot reach the registers screen', function () {
    (new RolesPermissionsSeeder)->run();
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Index::class)->assertForbidden();
});

test('an admin can create a register', function () {
    $admin = actingAsAdmin();
    $warehouse = Warehouse::default();

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('createRegister')
        ->set('name', 'Front Counter')
        ->set('code', 'POS-2')
        ->set('warehouse_id', $warehouse->id)
        ->call('saveRegister')
        ->assertSet('showModal', false);

    expect(PosRegister::where('code', 'POS-2')->exists())->toBeTrue();
});

test('a register with an open shift cannot be deleted', function () {
    $admin = actingAsAdmin();
    $register = PosRegister::create(['name' => 'Till', 'code' => 'T1', 'warehouse_id' => Warehouse::default()->id, 'is_active' => true]);
    PosShift::create(['register_id' => $register->id, 'opened_by' => $admin->id, 'opening_cash' => 0, 'status' => 'open', 'opened_at' => now()]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('deleteRegister', $register->id);

    expect(PosRegister::find($register->id))->not->toBeNull();
});
