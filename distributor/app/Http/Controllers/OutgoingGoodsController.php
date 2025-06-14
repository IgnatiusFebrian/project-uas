<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OutgoingGoods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OutgoingGoodsController extends Controller
{
    public function index()
    {
        $outgoingGoods = OutgoingGoods::with(['item', 'user'])->latest()->get();
        return view('outgoing_goods.index', compact('outgoingGoods'));
    }

    public function create()
    {
        $items = Item::all();
        return view('outgoing_goods.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $item = Item::findOrFail($validated['item_id']);

            if ($item->stock < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Quantity exceeds available stock.'])->withInput();
            }

            $validated['user_id'] = Auth::id();

            $outgoingGoods = OutgoingGoods::create($validated);

            // Update item stock
            $item->stock -= $validated['quantity'];
            $item->save();

            DB::commit();
            return redirect()->route('outgoing_goods.index')
                ->with('success', 'Barang keluar berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function report()
    {
        $outgoingTransactions = OutgoingGoods::with(['item', 'user'])
            ->latest()
            ->get();
        return view('outgoing_goods.report', compact('outgoingTransactions'));
    }

    public function edit($id)
    {
        $outgoingGood = OutgoingGoods::findOrFail($id);
        $items = Item::all();
        return view('outgoing_goods.edit', compact('outgoingGood', 'items'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $outgoingGood = OutgoingGoods::findOrFail($id);
            $item = Item::findOrFail($validated['item_id']);

            // Adjust stock based on quantity difference
            $quantityDifference = $validated['quantity'] - $outgoingGood->quantity;

            if ($item->stock < $quantityDifference) {
                return back()->withErrors(['quantity' => 'Quantity exceeds available stock.'])->withInput();
            }

            $item->stock -= $quantityDifference;
            $item->save();

            $validated['user_id'] = Auth::id();

            $outgoingGood->update($validated);

            DB::commit();
            return redirect()->route('outgoing_goods.index')
                ->with('success', 'Barang keluar berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $outgoingGood = OutgoingGoods::findOrFail($id);
        $item = $outgoingGood->item;

        // Adjust stock
        if ($item) {
            $item->stock += $outgoingGood->quantity;
            $item->save();
        }

        $outgoingGood->delete();

        return redirect()->route('outgoing_goods.index')
            ->with('success', 'Barang keluar berhasil dihapus');
    }
}
