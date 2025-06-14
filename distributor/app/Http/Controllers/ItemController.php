<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
public function index(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $outgoingSubquery = DB::table('outgoing_goods')
        ->select('item_id', DB::raw('SUM(quantity) as total_outgoing'))
        ->groupBy('item_id');

    $query = DB::table('items')
        ->leftJoinSub($outgoingSubquery, 'outgoing', function ($join) {
            $join->on('items.id', '=', 'outgoing.item_id');
        });

    if ($startDate) {
        $query->whereDate('items.created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('items.created_at', '<=', $endDate);
    }

    $items = $query->select(
            'items.id',
            'items.name',
            'items.stock',
            'items.price',
            DB::raw('COALESCE(outgoing.total_outgoing, 0) as total_outgoing')
        )
        ->get();

    return view('items.index', compact('items'));
}

    public function create()
    {
        $items = Item::all();
        return view('items.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'existing_item' => 'nullable|exists:items,id',
            'new_item_name' => 'nullable|string|required_without:existing_item',
            'stock' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($request->filled('existing_item')) {
            // Update stock of existing item
            $item = Item::findOrFail($request->existing_item);
            $item->stock += $request->stock;
            $item->save();
        } else {
            // Create new item
            Item::create([
                'name' => $request->new_item_name,
                'stock' => $request->stock,
                'price' => $request->price,
            ]);
        }

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
