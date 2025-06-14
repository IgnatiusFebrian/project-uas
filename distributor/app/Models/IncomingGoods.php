<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingGoods extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'date',
        'notes',
        'price'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
