<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosRegister extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(PosShift::class, 'register_id');
    }

    public function openShift(): ?PosShift
    {
        return $this->shifts()->where('status', 'open')->first();
    }

    /**
     * The tenant's default register, lazily created ("Main Register", tied to
     * the default warehouse) the first time POS is opened — mirrors
     * Warehouse::default() so a tenant can start using the till immediately
     * without first visiting the (Milestone 4) register management screen.
     */
    public static function default(): self
    {
        return static::firstOrCreate(
            ['is_active' => true],
            ['name' => 'Main Register', 'code' => 'POS-1', 'warehouse_id' => Warehouse::default()->id]
        );
    }
}
