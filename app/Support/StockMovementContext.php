<?php

namespace App\Support;

/**
 * Lets a call site declare *why* a stock change is happening (sale, return,
 * manual adjustment) without threading extra parameters through every
 * increment()/decrement()/update() call. ProductObserver and
 * ProductAttributeObserver read current() when logging a StockMovement.
 *
 * Stack-based so nested run() calls (e.g. an adjustment that touches both a
 * variant and, via syncPriceAndStock(), the parent product) compose safely.
 */
class StockMovementContext
{
    /**
     * @var array<int, array{type?: string, reason?: string|null, changed_by?: int|null}>
     */
    private static array $stack = [];

    /**
     * @param  array{type?: string, reason?: string|null, changed_by?: int|null}  $context
     */
    public static function run(array $context, \Closure $callback): mixed
    {
        self::$stack[] = $context;

        try {
            return $callback();
        } finally {
            array_pop(self::$stack);
        }
    }

    /**
     * @return array{type?: string, reason?: string|null, changed_by?: int|null}|null
     */
    public static function current(): ?array
    {
        return self::$stack[count(self::$stack) - 1] ?? null;
    }
}
