<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'phone',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    /**
     * The tenant's default warehouse, lazily created ("Main Warehouse"/"MAIN")
     * the first time it's needed — this is what keeps every single-warehouse
     * tenant's stock-mutation call sites behaving exactly as before.
     */
    public static function default(): self
    {
        return static::firstOrCreate(
            ['is_default' => true],
            ['name' => 'Main Warehouse', 'code' => 'MAIN', 'is_active' => true]
        );
    }
}
