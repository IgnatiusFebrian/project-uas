<?php

namespace App\Http\Controllers;

use App\Models\OutgoingGoods;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OutgoingGoodsController extends Controller
{
    public function index()
    {
        $outgoingGoods = OutgoingGoods::with(['item', 'user'])->get()->map(function ($item) {
            if ($item->date instanceof \Carbon\Carbon) {
                $item->formatted_date = $item->date->timezone('Asia/Jakarta')->format('d/m/Y H:i');
            } else {
                $item->formatted_date = Carbon::parse($item->date)->timezone('Asia/Jakarta')->format('d/m/Y H:i');
            }
            return $item;
        });

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
        ]);

        $validated['user_id'] = auth()->id();
        $item = Item::findOrFail($validated['item_id']);
        if ($item->stock < $validated['quantity']) {
            return redirect()->back()->withErrors(['quantity' => 'Stok barang tidak mencukupi.'])->withInput();
        }
        OutgoingGoods::create($validated);
        $item->stock -= $validated['quantity'];
        $item->save();

        return redirect()->route('outgoing_goods.index')->with('success', 'Barang keluar berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $outgoingGoods = OutgoingGoods::findOrFail($id);
        $items = Item::all();
        return view('outgoing_goods.edit', compact('outgoingGoods', 'items'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $outgoingGoods = OutgoingGoods::findOrFail($id);
        $outgoingGoods->update($validated);

        return redirect()->route('outgoing_goods.index')->with('success', 'Barang keluar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $outgoingGoods = OutgoingGoods::findOrFail($id);
        $outgoingGoods->delete();

        return redirect()->route('outgoing_goods.index')->with('success', 'Barang keluar berhasil dihapus.');
    }
}
