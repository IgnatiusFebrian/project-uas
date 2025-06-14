<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        if ($product->stock < $quantity) {
            return response()->json([
                'error' => 'Stock tidak mencukupi'
            ], 422);
        }

        $cart = session()->get('cart', []);

        $cart[$product->id] = [
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $product->price,
            'subtotal' => $product->price * $quantity
        ];

        session()->put('cart', $cart);

        return response()->json([
            'cart' => $cart,
            'total' => $this->getTotal()
        ]);
    }

    private function getTotal()
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'subtotal'));
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (!$product->decreaseStock($item['quantity'])) {
                return back()->with('error', 'Stock tidak mencukupi');
            }
        }

        session()->forget('cart');
        return redirect()->route('orders.success');
    }
}
