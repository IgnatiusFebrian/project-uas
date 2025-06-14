<?php

namespace App\Http\Controllers;

use App\Models\ReturnGoods;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnGoodsController extends Controller
{
    public function index()
    {
        $returns = ReturnGoods::with(['item', 'user'])->latest()->get();
        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $items = Item::all();
        return view('returns.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $item = Item::findOrFail($validated['item_id']);

            if ($item->stock < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Quantity exceeds available stock.'])->withInput();
            }

            $validated['user_id'] = Auth::id();

            $returnGoods = ReturnGoods::create($validated);

            // Reduce item stock
            $item->stock -= $validated['quantity'];
            $item->save();

            DB::commit();
            return redirect()->route('returns.index')
                ->with('success', 'Return recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error occurred: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $returnGoods = ReturnGoods::findOrFail($id);
        $items = Item::all();
        return view('returns.edit', compact('returnGoods', 'items'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $returnGoods = ReturnGoods::findOrFail($id);
            $item = Item::findOrFail($validated['item_id']);

            // Adjust stock based on quantity difference
            $quantityDifference = $validated['quantity'] - $returnGoods->quantity;

            if ($quantityDifference > 0 && $item->stock < $quantityDifference) {
                return back()->withErrors(['quantity' => 'Quantity exceeds available stock.'])->withInput();
            }

            $item->stock -= $quantityDifference;
            $item->save();

            $validated['user_id'] = Auth::id();

            $returnGoods->update($validated);

            DB::commit();
            return redirect()->route('returns.index')
                ->with('success', 'Return updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error occurred: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $returnGoods = ReturnGoods::findOrFail($id);
        $item = $returnGoods->item;

        // Adjust stock
        if ($item) {
            $item->stock += $returnGoods->quantity;
            $item->save();
        }

        $returnGoods->delete();

        return redirect()->route('returns.index')
            ->with('success', 'Return deleted successfully.');
    }
}
