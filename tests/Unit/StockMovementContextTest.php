<?php

declare(strict_types=1);

use App\Enums\StockMovementType;
use App\Support\StockMovementContext;

test('current() is null when no context is active', function () {
    expect(StockMovementContext::current())->toBeNull();
});

test('run() exposes the declared context to current() only while the callback executes', function () {
    $seen = null;

    StockMovementContext::run(['type' => StockMovementType::Adjustment, 'reason' => 'test'], function () use (&$seen) {
        $seen = StockMovementContext::current();
    });

    expect($seen)->toBe(['type' => StockMovementType::Adjustment, 'reason' => 'test']);
    expect(StockMovementContext::current())->toBeNull();
});

test('nested run() calls push and pop in LIFO order', function () {
    $seenDuringInner = null;
    $seenAfterInner = null;

    StockMovementContext::run(['type' => StockMovementType::Sale], function () use (&$seenDuringInner, &$seenAfterInner) {
        StockMovementContext::run(['type' => StockMovementType::Adjustment], function () use (&$seenDuringInner) {
            $seenDuringInner = StockMovementContext::current();
        });

        $seenAfterInner = StockMovementContext::current();
    });

    expect($seenDuringInner)->toBe(['type' => StockMovementType::Adjustment]);
    expect($seenAfterInner)->toBe(['type' => StockMovementType::Sale]);
    expect(StockMovementContext::current())->toBeNull();
});

test('the stack pops even when the callback throws', function () {
    try {
        StockMovementContext::run(['type' => StockMovementType::Adjustment], function () {
            throw new RuntimeException('boom');
        });
    } catch (RuntimeException $e) {
        // expected
    }

    expect(StockMovementContext::current())->toBeNull();
});
