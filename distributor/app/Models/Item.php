<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'stock', 'unit', 'price', 'minimum_stock', 'user_id'];
    protected $guarded = ['id'];

    public function incomingGoods()
    {
        return $this->hasMany(\App\Models\IncomingGoods::class, 'item_id');
    }

    public function outgoingGoods()
    {
        return $this->hasMany(\App\Models\OutgoingGoods::class, 'item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($item) {
            if ($item->isDirty('price') && !auth()->user()->is_admin) {
                throw new \Exception('Only administrators can modify item prices.');
            }
        });
    }
}
