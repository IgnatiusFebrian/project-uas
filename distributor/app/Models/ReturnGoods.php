<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnGoods extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'item_id',
        'quantity',
        'date',
        'reason',
        'user_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
