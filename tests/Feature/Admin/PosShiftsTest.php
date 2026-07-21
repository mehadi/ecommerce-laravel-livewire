<?php

declare(strict_types=1);

use App\Livewire\Admin\Pos\Shifts\Index;
use App\Models\PosRegister;
use App\Models\PosShift;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('a manager can force-close an open shift using the computed expected cash', function () {
    $manager = actingAsAdmin();
    $register = PosRegister::create(['name' => 'Till', 'code' => 'T1', 'warehouse_id' => Warehouse::default()->id, 'is_active' => true]);
    $shift = PosShift::create(['register_id' => $register->id, 'opened_by' => $manager->id, 'opening_cash' => 100, 'status' => 'open', 'opened_at' => now()]);

    Livewire::actingAs($manager)
        ->test(Index::class)
        ->call('forceCloseShift', $shift->id);

    $shift->refresh();
    expect($shift->status)->toBe('closed');
    expect($shift->variance)->toEqual(0.0);
    expect($shift->closing_cash)->toEqual(100.0);
});

test('force-closing an already-closed shift is a no-op', function () {
    $manager = actingAsAdmin();
    $register = PosRegister::create(['name' => 'Till', 'code' => 'T1', 'warehouse_id' => Warehouse::default()->id, 'is_active' => true]);
    $shift = PosShift::create([
        'register_id' => $register->id, 'opened_by' => $manager->id, 'opening_cash' => 100,
        'status' => 'closed', 'opened_at' => now()->subHour(), 'closed_at' => now(), 'closing_cash' => 100, 'expected_cash' => 100, 'variance' => 0,
    ]);

    Livewire::actingAs($manager)->test(Index::class)->call('forceCloseShift', $shift->id);

    expect($shift->fresh()->closed_at->eq($shift->closed_at))->toBeTrue();
});
