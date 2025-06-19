<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingGoods extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'price',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
