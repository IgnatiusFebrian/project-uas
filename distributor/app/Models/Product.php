<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function addStock($quantity, $notes = '')
    {
        $this->stock += $quantity;
        $this->save();

        return $this->inventoryMovements()->create([
            'quantity' => $quantity,
            'type' => 'in',
            'notes' => $notes,
            'color' => 'green'
        ]);
    }

    public function decreaseStock($quantity, $notes = '')
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->save();

            $this->inventoryMovements()->create([
                'quantity' => $quantity,
                'type' => 'out',
                'notes' => $notes,
                'color' => 'red'
            ]);

            return true;
        }
        return false;
    }

    public function getStockMovementSummary()
    {
        return [
            'total_in' => $this->inventoryMovements()->where('type', 'in')->sum('quantity'),
            'total_out' => $this->inventoryMovements()->where('type', 'out')->sum('quantity'),
            'current_stock' => $this->stock
        ];
    }
}
