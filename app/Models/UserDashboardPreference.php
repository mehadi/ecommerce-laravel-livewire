<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDashboardPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_key',
        'order',
        'is_visible',
        'card_type',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
